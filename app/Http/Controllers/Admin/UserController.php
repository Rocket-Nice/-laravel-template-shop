<?php

namespace App\Http\Controllers\Admin;

use App\Exports\UserPermissionsExport;
use App\Exports\UsersExport;
use App\Http\Controllers\Controller;
use App\Jobs\ExportUsersJob;
use App\Models\Bonus;
use App\Models\BonusTransaction;
use App\Models\City;
use App\Models\Country;
use App\Models\MailingList;
use App\Models\Region;
use App\Models\User;
use App\Models\ExportFile;
use App\Services\MailSender;
use App\Services\TelegramSender;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;
use SafeObject;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    public function index()
    {
        $users = User::select()->filter(new SafeObject(request()->toArray()));
        $users = $users->orderBy('id', 'desc')->paginate(50);
        $countries = Country::select('id', 'name', 'options')->orderAvailable()->get();
        $mailing_lists = null;
        if (auth()->user()->hasPermissionTo('Управление рассылками')) {
            $mailing_lists = MailingList::query()->select('name', 'id')->orderByDesc('id')->get();
        }
        $seo = [
            'title' => 'Все пользователи'
        ];
        return view('template.admin.users.index', compact('users', 'seo', 'countries', 'mailing_lists'));
    }

    public function create()
    {
        $roles = Role::all();
        $seo = [
            'title' => 'Добавить пользователя'
        ];
        return view('template.admin.users.create', compact('seo', 'roles'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email:rfc,dns|max:255|unique:users,email',
            'phone' => 'nullable|string|regex:/^([0-9\s\-\+\(\)]*)$/|max:255',
            'password' => 'required|string',
        ]);
        $email = strtolower($request->email);
        $phone = preg_replace("/[^,.0-9]/", '', $request->phone);
        $phone = preg_replace('/^(89|79|9)/', '+79', $phone);
        if ($phone[0] == '9') {
            $phone = '+7' . $phone;
        }
        $user = User::create([
            'uuid' => (string) Str::uuid(),
            'name' => $request->name,
            'email' => $email,
            'phone' => $phone,
            'password' => Hash::make($request->password),
        ]);
        if ($request->has('role') && !empty($request->get('role'))) {
            $roles = $user->getRoleNames();
            foreach ($roles as $role) {
                $user->removeRole($role);
            }
            $user->assignRole($request->get('role'));
        } else {
            $roles = $user->getRoleNames();
            foreach ($roles as $role) {
                $user->removeRole($role);
            }
        }
        if ($request->has('permissions') && !empty($request->get('permissions'))) {
            $request_role = $request->get('role');
            $permissions = Permission::select('id')->whereDoesntHave('roles', function ($query) use ($request_role) {
                $query->where('id', $request_role);
            })->pluck('id')->toArray();
            $permissions_result = array_intersect($request->get('permissions'), $permissions);
            $user->syncPermissions($permissions_result);
        } else {
            $user->syncPermissions();
        }
        return redirect()->route('admin.users.index')->with([
            'success' => 'Новый пользователь создан'
        ]);
    }

    public function edit(User $user)
    {
        if ($user->hasPermissionTo('Доступ к админпанели') && !auth()->user()->hasRole('admin')) {
            return back()->withErrors(['Нет доступа']);
        }
        $roles = Role::all();
        $user_roles = $user->getRoleNames()->toArray();
        $permissions = Permission::whereDoesntHave('roles', function ($query) use ($user_roles) {
            $query->whereIn('name', $user_roles);
        })->get();

        $puzzleImages = $user->puzzleImages;
        $seo = [
            'title' => 'Изменить данные пользователя'
        ];
        return view('template.admin.users.edit', compact('seo', 'user', 'roles', 'permissions', 'puzzleImages'));
    }

    public function update(Request $request, User $user)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email:rfc,dns|max:255',
            'phone' => 'nullable|string|regex:/^([0-9\s\-\+\(\)]*)$/|max:255'
        ]);
        if ($user->hasPermissionTo('Доступ к админпанели') && !auth()->user()->hasRole('admin')) {
            return back()->withErrors(['Нет доступа']);
        }
        if (User::where('email', $request->email)->where('id', '!=', $user->id)->count()) {
            return back()->withInput()->withErrors([
                'Данный email уже используется другим пользователем'
            ]);
        }

        $email = strtolower($request->email);
        $phone = preg_replace("/[^,.0-9]/", '', $request->phone);
        $phone = preg_replace('/^(89|79|9)/', '+79', $phone);
        if ($phone[0] == '9') {
            $phone = '+7' . $phone;
        }


        $user_params = [
            'name' => $request->name,
            'email' => $email,
            'phone' => $phone
        ];
        $birthday = request()->birthday ? Carbon::createFromFormat('d.m.Y', request()->birthday)->format('Y-m-d') : null;

        $user_params['birthday'] = $birthday;
        if (!empty($request->password)) {
            $user_params['password'] = Hash::make($request->password);
        }
        $user->update($user_params);

        if (auth()->user()->hasRole('admin')) {
            if ($request->has('role') && !empty($request->get('role'))) {
                $roles = $user->getRoleNames();
                foreach ($roles as $role) {
                    $user->removeRole($role);
                }
                $user->assignRole($request->get('role'));
            } else {
                $roles = $user->getRoleNames();
                foreach ($roles as $role) {
                    $user->removeRole($role);
                }
            }
            if ($request->has('permissions') && !empty($request->get('permissions'))) {
                $request_role = $request->get('role');
                $permissions = Permission::select('id')->whereDoesntHave('roles', function ($query) use ($request_role) {
                    $query->where('id', $request_role);
                })->pluck('id')->toArray();
                $permissions_result = array_intersect($request->get('permissions'), $permissions);
                $user->syncPermissions($permissions_result);
            } else {
                $user->syncPermissions();
            }
        }

        return redirect()->route('admin.users.edit', $user->id)->with([
            'success' => 'Данные пользователя успешно изменены'
        ]);
    }

    public function auth(User $user)
    {
        if ($user->hasPermissionTo('Доступ к админпанели') && !auth()->user()->hasRole('admin')) {
            return back()->withErrors(['Нет доступа']);
        }
        Auth::loginUsingId($user->id);
        return redirect()->route('page.index')->with([
            'success' => 'Вы авторизованы под пользователем «' . $user->email . '»'
        ]);
    }

    public function createApiToken(User $user)
    {
        if ($user->tokens->isNotEmpty()) {
            foreach ($user->tokens as $t) {
                $t->delete();
            }
        }
        $token_name = 'API by ' . auth()->id() . ' ' . now()->format('d.m.Y H:i');
        $token = $user->createToken($token_name)->plainTextToken;
        (new MailSender($user->email))->sendApiToken($token);
        $user->addLog('Создан API-токен для пользователя: ' . $token_name);
        return redirect()->route('admin.users.edit', $user->id)->with([
            'success' => 'Новый ключ для подключения к api успешно создан и отправлен на email пользователя'
        ]);
    }

    public function admins()
    {
        $users = User::permission('Доступ к админпанели');
        if (request()->name) {
            $users->where(DB::raw('lower(name)'), 'like', '%' . trim(request()->name) . '%');
        }
        if (request()->email) {
            $users->where(DB::raw('lower(email)'), 'like', '%' . trim(request()->email) . '%');
        }
        if (request()->phone) {
            $users->where(DB::raw('lower(phone)'), 'like', '%' . trim(request()->phone) . '%');
        }
        $users = $users->orderBy('id', 'desc')->paginate(50);
        $seo = [
            'title' => 'Пользователи с доступом к админпанели'
        ];
        return view('template.admin.users.admins', compact('users', 'seo'));
    }

    public function addBonuses(User $user, Request $request)
    {
        $request->validate([
            'bonuses' => 'required|numeric',
            'comment' => 'nullable'
        ]);
        if (!$request->super) {
            $user->addBonuses($request->bonuses, $request->comment, now()->addMonth()->endOfDay());
            $user->addLog('Начислены бонусы (' . $request->bonuses . ')', $request->comment);
            $message = 'На счет пользователя начислено ' . denum($request->bonuses, ['%d бонус', '%d бонуса', '%d бонусов']);
        } else {
            $user->addSuperBonuses($request->bonuses, $request->comment);
            $user->addLog('Начислены 💎 бонусы (' . $request->bonuses . ')', $request->comment);
            $message = 'На счет пользователя начислено ' . denum($request->bonuses, ['%d 💎 бонус', '%d 💎 бонуса', '%d 💎 бонусов']);
        }

        return redirect()->route('admin.users.edit', $user->id)->with([
            'success' => $message
        ]);
    }
    public function subBonuses(User $user, Request $request)
    {
        $request->validate([
            'bonuses' => 'required|numeric',
            'comment' => 'nullable'
        ]);
        if (!$request->super) {
            $user->subBonuses($request->bonuses, $request->comment);
            $user->addLog('Списаны бонусы (' . $request->bonuses . ')', $request->comment);
            $message = 'Со счета пользователя списано ' . denum($request->bonuses, ['%d бонус', '%d бонуса', '%d бонусов']);
        } else {
            $user->subSuperBonuses($request->bonuses, $request->comment);
            $user->addLog('Списаны 💎 бонусы (' . $request->bonuses . ')', $request->comment);
            $message = 'Со счета пользователя списано ' . denum($request->bonuses, ['%d 💎 бонус', '%d 💎 бонуса', '%d 💎 бонусов']);
        }

        return redirect()->route('admin.users.edit', $user->id)->with([
            'success' => $message
        ]);
    }

    public function export(Request $request)
    {
        ExportUsersJob::dispatch($request->toArray(), 1, auth()->id())->onQueue('export_users');
        return back()->with([
            'success' => 'Задача на экспорт пользователей создана'
        ]);
    }

    //  public function export_page(){
    //    $directory = 'public/export/users/'; // замените на ваш путь
    //
    //
    //      // Получаем все файлы в директории
    //    $files = Storage::files($directory);
    //    // Фильтруем файлы, оставляя только с расширением .xlsx
    //    $filtered = collect($files)->filter(function ($file) {
    //      return pathinfo($file, PATHINFO_EXTENSION) === 'xlsx';
    //    });
    //
    //    // Создаем коллекцию для хранения информации о файлах
    //    $collection = collect([]);
    //
    //    foreach ($filtered as $file) {
    //      // Получаем путь, имя файла и другие атрибуты
    //      $filePath = storage_path('app/' . $file);
    //      $fileName = pathinfo($file, PATHINFO_FILENAME) . '.' . pathinfo($file, PATHINFO_EXTENSION);
    //      $fileSize = Storage::size($file);
    //      $fileDate = date('d.m.Y H:i:s', Storage::lastModified($file));
    //      $fileUrl = Storage::url($file); // Ссылка для скачивания
    //
    //      // Добавляем информацию о файле в коллекцию
    //      $collection->push([
    //          'name' => $fileName,
    //          'size' => $fileSize,
    //          'date' => $fileDate,
    //          'url'  => $fileUrl,
    //          'last_modified' => Storage::lastModified($file)
    //      ]);
    //    }
    //
    //// Сортируем коллекцию по дате создания файла в порядке убывания
    //    $sortedCollection = $collection->sortByDesc('last_modified')->values()->all();
    //
    //    $jobsCount = DB::table('jobs')->where('queue', 'export_users')->count() ? true : false;
    //
    //    $seo = [
    //        'title' => 'Экспорт пользователей'
    //    ];
    //    return view('template.admin.users.export', compact('seo', 'sortedCollection', 'jobsCount'));
    //  }

    public function exportPermissions(Request $request)
    {
        $export = new UserPermissionsExport($request);
        $file_name = 'user-permissions_' . now()->format('d-m-Y_H-i') . '.xlsx';
        $file_path = 'public/export/users/' . $file_name;
        if (!file_exists(storage_path('app/public/export/users'))) {
            mkdir(storage_path('app/public/export/users'), 0777, true);
        }
        $roles = Role::whereHas('permissions', function ($query) {
            $query->where('name', 'Доступ к админпанели');
        })->pluck('name')->toArray();

        $count = User::query()
            ->whereHas('roles', function ($query) use ($roles) {
                $query->whereIn('name', $roles);
            })
            ->orWhereHas('permissions', function ($query) {
                $query->where('name', 'Доступ к админпанели');
            })->count();
        ExportFile::create([
            'name' => $file_name,
            'path' => $file_path,
            'type',
            'lines_count' => $count,
            'exported_by' => auth()->id(),
        ]);
        Excel::store($export, 'export/users/' . $file_name, 'public');
        return back()->with([
            'success' => 'Задача на экспорт пользователей создана'
        ]);
    }

    public function export_job($request, $user_id): void
    {
        $export = new UsersExport($request);
        $file_name = 'users_' . now()->format('d-m-Y_H-i') . '.xlsx';
        $file_path = 'public/export/users/' . $file_name;
        $count = \App\Models\User::query()->select('id')->filter(new SafeObject($request))->count();
        ExportFile::create([
            'name' => $file_name,
            'path' => $file_path,
            'type',
            'lines_count' => $count,
            'exported_by' => $user_id,
        ]);
        Excel::store($export, $file_path);
    }

    public function regions(Request $request)
    {
        $request->validate([
            'country' => 'required'
        ]);
        $regions = Region::select('id', 'name')
            ->where('country_id', $request->country)
            ->orderBy('name')->get();

        return $regions->toArray();
    }

    public function cities(Request $request)
    {
        $request->validate([
            'country' => 'required',
            'region' => 'required',
        ]);
        $regions = City::select('id', 'name')
            ->where('country_id', $request->country)
            ->where('region_id', $request->region)
            ->orderBy('name')->get();

        return $regions->toArray();
    }

    public function birthdayGifts(): void
    {
        $users = User::query()->select('id', 'email')->whereRaw('MONTH(birthday) = ?', [now()->addDays(3)->month])
            ->whereRaw('DAY(birthday) = ?', [now()->addDays(3)->day])
            ->whereDoesntHave('bonus_transactions', function (Builder $builder) {
                $builder->where('created_at', '>', now()->subDays(360)->format('Y-m-d H:i:s'));
                $builder->where('comment', 'birthday');
            })
            ->get();
        foreach ($users as $user) {
            $user->addBonuses(500, 'birthday');
            (new MailSender($user->email))->birthdayGreetings(500);
            foreach ($user->tgChats as $tgChat) {
                (new TelegramSender($tgChat))->birthdayGreetings(500);
            }
        }
    }

    public function expireBonuses(): void
    {
        $bonuses = Bonus::query()->where('expired_at', '<', now())->where('amount', '>', 0)->get();
        foreach ($bonuses as $bonus) {
            $user = $bonus->user;
            BonusTransaction::create([
                'bonus_id' => $bonus->id,
                'user_id' => $user->id,
                'amount' => $bonus->amount,
                'comment' => 'expired',
                'created_by' => null,
            ]);
            $bonus->update([
                'amount' => 0
            ]);
        }
    }

    public function surveyGifts()
    {
        $users = User::query()->select('id', 'email')
            ->whereDoesntHave('bonus_transactions', function (Builder $builder) {
                $builder->where('comment', '_Анкета');
            })
            ->has('surveysForBonuses')
            ->get();
        foreach ($users as $user) {
            $user->addBonuses(250, '_Анкета');
        }
        return $users->count();
    }

    public function telegramGifts()
    {
        $users = User::query()->select('id', 'email')
            ->whereDoesntHave('bonus_transactions', function (Builder $builder) {
                $builder->where('comment', 'like', 'telegram%');
            })
            ->whereHas('tgChats', function (Builder $builder) {
                $builder->where('created_at', '>', '2024-07-01 00:00:00');
                $builder->where('created_at', '<', now()->subDays(6)->format('Y-m-d 00:00:00'));
                $builder->where('active', true);
            })
            ->get();
        foreach ($users as $user) {
            $user->addBonuses(250, 'telegram');
        }
        return $users->count();
    }
}
