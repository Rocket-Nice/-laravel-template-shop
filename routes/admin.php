<?php

use Illuminate\Support\Facades\Route;


Route::name('admin.')->group(function () {
  Route::group(['prefix' => 'laravel-filemanager', 'middleware' => ['web', 'auth']], function () {
    \UniSharp\LaravelFilemanager\Lfm::routes();
  });

  Route::get('/export-data', [\App\Http\Controllers\Admin\ExportFileController::class, 'index'])->name('export_data.index');

  Route::middleware(['permission:Доступ к отчетам'])->group(function () {
    Route::get('/reports', [\App\Http\Controllers\Admin\ReportController::class, 'index'])->name('reports.index');
    Route::get('/reports/export', [\App\Http\Controllers\Admin\ReportController::class, 'export'])->name('reports.export');
    Route::get('/reports/chart1', [\App\Http\Controllers\Admin\ReportController::class, 'orderTotalsByPeriod']);
    Route::get('/reports/statuses', [\App\Http\Controllers\Admin\ReportController::class, 'reportStatusesPage'])->name('reports.statuses');
    Route::get('/reports/statuses-data', [\App\Http\Controllers\Admin\ReportController::class, 'reportStatusesData']);
    Route::get('/reports/shipping', [\App\Http\Controllers\Admin\ReportController::class, 'reportShippingPage'])->name('reports.shipping');
    Route::get('/reports/shipping-data', [\App\Http\Controllers\Admin\ReportController::class, 'reportShippingData']);
    Route::get('/reports/products', [\App\Http\Controllers\Admin\ReportController::class, 'reportProductsPage'])->name('reports.products');
    Route::get('/reports/products-data', [\App\Http\Controllers\Admin\ReportController::class, 'reportProductsData']);
    Route::get('/reports/new-users', [\App\Http\Controllers\Admin\ReportController::class, 'reportNewUsersPage'])->name('reports.new-users');
    Route::get('/reports/new-users-data', [\App\Http\Controllers\Admin\ReportController::class, 'reportNewUsersData']);
    Route::get('/reports/average-check', [\App\Http\Controllers\Admin\ReportController::class, 'reportAverageCheckPage'])->name('reports.average-check');
    Route::get('/reports/average-check-data', [\App\Http\Controllers\Admin\ReportController::class, 'reportAverageCheckData']);
    Route::get('/reports/finished-orders', [\App\Http\Controllers\Admin\ReportController::class, 'reportFinishedOrdersPage'])->name('reports.finished-orders');
    Route::get('/reports/finished-orders-data', [\App\Http\Controllers\Admin\ReportController::class, 'reportFinishedOrdersData']);
    Route::get('/reports/links', [\App\Http\Controllers\Admin\ReportController::class, 'linksPage'])->name('reports.links');
    Route::get('/reports/links-data', [\App\Http\Controllers\Admin\ReportController::class, 'linksData']);
    Route::get('/reports/links/{link}', [\App\Http\Controllers\Admin\ReportController::class, 'linkPage'])->name('reports.link');
    Route::get('/reports/links-data/{link}', [\App\Http\Controllers\Admin\ReportController::class, 'linkData']);
  });
  Route::middleware(['permission:Управления артикулами'])->group(function () {
    Route::resource('/product-skus', '\App\Http\Controllers\Admin\ProductSkuController', ['except' => ['show']]);
  });
  Route::middleware(['permission:Рассылка в телеграм'])->group(function () {
//    Route::get('/telegram_mailing', [\App\Http\Controllers\Admin\TelegramController::class, 'mailing'])->name('telegram_mailing.index');
//    Route::post('/telegram_mailing/store', [\App\Http\Controllers\Admin\TelegramController::class, 'mailing_send'])->name('telegram_mailing.send');
    Route::delete('/telegram_mailing/cancel', [\App\Http\Controllers\Admin\TelegramMailingController::class, 'mailing_cancel'])->name('telegram_mailing.cancel');
    Route::post('/telegram_mailing/send/{id}', [\App\Http\Controllers\Admin\TelegramMailingController::class, 'prepareForSending'])->name('telegram_mailing.send');
    Route::resource('/telegram_mailing', '\App\Http\Controllers\Admin\TelegramMailingController', ['except' => ['show']]);
  });
  Route::get('/', [\App\Http\Controllers\Admin\HomeController::class, 'index'])->name('page.index');
  Route::middleware(['role:admin'])->get('/updateDB', [\App\Http\Controllers\Admin\SystemController::class, 'updateDB']);
  Route::get('/closed', [\App\Http\Controllers\Admin\SystemController::class, 'closed']);
  Route::middleware(['role:admin'])->get('/test', [\App\Http\Controllers\Admin\SystemController::class, 'test']);
  Route::middleware(['role:admin'])->post('/test', [\App\Http\Controllers\Admin\SystemController::class, 'post']);
  Route::middleware(['role:admin'])->get('/emails-by-city', [\App\Http\Controllers\Admin\SystemController::class, 'emailsByCity']);

  Route::middleware(['permission:Доступ к НПС'])->group(function () {
    Route::get('/nps/statistic', [\App\Http\Controllers\Admin\SurveyController::class, 'statistic'])->name('nps.statistic');
    Route::get('/nps/comments', [\App\Http\Controllers\Admin\SurveyController::class, 'comments'])->name('nps.comments');
    Route::post('/nps/{user}/survey', [\App\Http\Controllers\Admin\SurveyController::class, 'survey'])->name('nps.survey');
    Route::post('/nps/{survey_user_id}', [\App\Http\Controllers\Admin\SurveyController::class, 'status'])->name('nps.status');
  });
  Route::middleware(['permission:Доступ к кастомным формам'])->group(function () {
    Route::get('/custom-forms/data', [\App\Http\Controllers\Admin\CustomFormController::class, 'data'])->name('custom-forms.data');
    Route::post('/custom-forms/data/{form_user_id}', [\App\Http\Controllers\Admin\CustomFormController::class, 'status'])->name('custom-forms.status');
    Route::post('/custom-forms/data/{form_id}/{field_id}/{user_id}', [\App\Http\Controllers\Admin\CustomFormController::class, 'change'])->name('custom-forms.change');
  });
  Route::middleware(['permission:Управление кастомными формами'])->group(function () {
    Route::resource('/custom-forms', '\App\Http\Controllers\Admin\CustomFormController', ['except' => ['show']])->parameters([
        'form' => 'form:slug',
    ]);
  });
  Route::group(['prefix' => 'blog', 'as' => 'blog.'], function () {
    Route::middleware(['permission:Управление категориями блога'])->group(function () {
      Route::resource('/categories', '\App\Http\Controllers\Admin\Blog\BlogCategoryController', ['except' => ['show']])->parameters([
          'сategory' => 'сategory:slug',
      ]);
    });
    Route::middleware(['permission:Управление статьями блога'])->group(function () {
      Route::resource('/articles', '\App\Http\Controllers\Admin\Blog\BlogArticleController', ['except' => ['show']])->parameters([
          'article' => 'article:slug',
      ]);
    });
  });

  Route::middleware(['permission:Просмотр пазлов для акции'])->group(function () {
    Route::resource('/promo/puzzles', '\App\Http\Controllers\Admin\Promo\PuzzlePrizeController', ['except' => ['show']]);
    Route::get('/promo/participants', [\App\Http\Controllers\Admin\Promo\PuzzleParticipantsController::class, 'index'])->name('puzzle_participants.index');
    Route::put('/promo/participants/{puzzleImage}', [\App\Http\Controllers\Admin\Promo\PuzzleParticipantsController::class, 'update'])->name('puzzle_participants.update');
  });
  Route::middleware(['permission:Уведомления в телеграм'])->group(function () {
    Route::get('/telegram-notifications', [\App\Http\Controllers\Admin\TelegramController::class, 'index'])->name('tg_notifications.index');
    Route::get('/telegram-notifications/settings', [\App\Http\Controllers\Admin\TelegramController::class, 'settings'])->name('tg_notifications.settings');
    Route::post('/telegram-notifications/settings', [\App\Http\Controllers\Admin\TelegramController::class, 'save'])->name('tg_notifications.save');
    Route::get('/telegram-notifications/{tgChat}', [\App\Http\Controllers\Admin\TelegramController::class, 'show'])->name('tg_notifications.show');
    Route::get('/telegram-notifications/{tgChat}/messages', [\App\Http\Controllers\Admin\TelegramController::class, 'messages'])->name('tg_notifications.messages');
    Route::post('/telegram-notifications/{tgChat}/send', [\App\Http\Controllers\Admin\TelegramController::class, 'send'])->name('tg_notifications.send');
  });
  Route::middleware(['permission:Управление партнерами'])->group(function () {
    Route::middleware(['permission:Редактирование партнеров'])->post('/happy-coupone/partners/settings', [\App\Http\Controllers\Admin\HappyCoupon\PartnerController::class, 'settings'])->name('partners.settings');
    Route::get('/partners/{partner:slug}/statistic', [\App\Http\Controllers\Admin\HappyCoupon\PartnerController::class, 'statistic'])->name('partners.statistic');
    Route::resource('/happy-coupone/partners', '\App\Http\Controllers\Admin\HappyCoupon\PartnerController', ['except' => ['show']])->parameters([
        'partner' => 'partner:slug',
    ]);
  });
  Route::middleware(['permission:Управление подарками'])->group(function () {
    Route::resource('/happy-coupone/prizes', '\App\Http\Controllers\Admin\HappyCoupon\PrizeController', ['except' => ['show']]);
  });
  Route::middleware(['permission:Управление счастливым купоном'])->group(function () {
    Route::get('/happy-coupone/activePrizes', [\App\Http\Controllers\Admin\HappyCoupon\PrizeController::class, 'activePrizes'])->name('happy_coupones.activePrizes');
    Route::post('/happy-coupone/activePrizes', [\App\Http\Controllers\Admin\HappyCoupon\PrizeController::class, 'activePrizesUpdate'])->name('happy_coupones.activePrizesUpdate');
  });
  Route::middleware(['permission:Просмотр купонов "СК"'])->group(function () {
    Route::get('/happy-coupone/coupones', [\App\Http\Controllers\Admin\HappyCoupon\GiftCouponController::class, 'index'])->name('happy_coupones.index');
  });
  Route::middleware(['permission:Управление кодами магазинов для СК'])->group(function () {
    Route::get('/happy-coupone/stores', [\App\Http\Controllers\Admin\HappyCoupon\StoreCouponController::class, 'index'])->name('happy_coupones.stores');
  });

  Route::middleware(['permission:Отгрузка заказов'])->group(function () {
    Route::get('/shipping/log', [\App\Http\Controllers\Admin\Shipping\ShippingLogController::class, 'index'])->name('shipping.log');
  });
  Route::middleware(['permission:Роли и разрешения'])->group(function () {
    Route::resource('/access/roles', '\App\Http\Controllers\Admin\Access\RoleController', ['except' => ['show']]);
    Route::resource('/access/permissions', '\App\Http\Controllers\Admin\Access\PermissionController', ['except' => ['show']]);
    Route::get('/access/admins', [\App\Http\Controllers\Admin\UserController::class, 'admins'])->name('users.admins');
  });
  Route::middleware(['permission:Управление контентом'])->group(function () {
    Route::resource('/content', '\App\Http\Controllers\Admin\ContentController');
    Route::get('/product-group/products', [\App\Http\Controllers\Admin\Goods\ProductGroupController::class, 'getProducts'])->name('product-group.products');
    Route::resource('/product-group', '\App\Http\Controllers\Admin\Goods\ProductGroupController');
  });
  Route::middleware(['permission:Управление страницами'])->group(function () {
    Route::resource('/pages', '\App\Http\Controllers\Admin\PageController', ['except' => ['show']])->parameters([
        'page' => 'page:slug',
    ]);
  });
  Route::middleware(['permission:Управление доставкой'])->group(function () {
    Route::resource('/shipping_methods', '\App\Http\Controllers\Admin\Shipping\ShippingMethodController', ['except' => ['show']]);
    Route::post('/cdek/import-new-territories', [\App\Http\Controllers\Admin\Shipping\ShippingMethodController::class, 'importNewTerritories'])->name('cdek.import-new-territories');

    Route::get('/countries', [\App\Http\Controllers\Admin\Shipping\CountriesController::class, 'index'])->name('shipping.countries.index');
    Route::put('/countries/updateCounties', [\App\Http\Controllers\Admin\Shipping\CountriesController::class, 'updateCounties'])->name('shipping.countries.updateCounties');
    Route::get('/countries/{county}', [\App\Http\Controllers\Admin\Shipping\CountriesController::class, 'edit'])->name('shipping.countries.edit');
    Route::put('/countries/{county}', [\App\Http\Controllers\Admin\Shipping\CountriesController::class, 'update'])->name('shipping.countries.update');
  });


  Route::middleware(['permission:Просмотр поисковых запросов'])->group(function () {
    Route::resource('/search_queries', '\App\Http\Controllers\Admin\SearchQueryController', ['except' => ['show']]);
  });
  Route::middleware(['permission:Просмотр промокодов'])->group(function () {
    Route::resource('/coupones', '\App\Http\Controllers\Admin\Promo\CouponeController', ['except' => ['show']]);
  });
  Route::middleware(['permission:Обнуление промокодов'])->group(function () {
    Route::post('/coupones/reset/{coupone}', [\App\Http\Controllers\Admin\Promo\CouponeController::class, 'reset_promocode'])->name('coupones.reset');
  });
  Route::middleware(['permission:Пакетное добавление сертификатов'])->group(function () {
    Route::get('/vouchers/batch', [\App\Http\Controllers\Admin\Promo\VoucherController::class, 'batch_create'])->name('vouchers.batch_create');
    Route::post('/vouchers/batch', [\App\Http\Controllers\Admin\Promo\VoucherController::class, 'batch_store'])->name('vouchers.batch_store');
  });
  Route::middleware(['permission:Просмотр подарочных сертификатов'])->group(function () {
    Route::resource('/vouchers', '\App\Http\Controllers\Admin\Promo\VoucherController', ['except' => ['show']]);
  });
  Route::middleware(['permission:Обнуление подарочных сертификатов'])->group(function () {
    Route::post('/vouchers/reset/{voucher}', [\App\Http\Controllers\Admin\Promo\VoucherController::class, 'reset_voucher'])->name('vouchers.reset');
  });

  Route::middleware(['permission:Выгрузка заказов'])->group(function () {
    Route::post('/orders/export', [\App\Http\Controllers\Admin\OrderController::class, 'export'])->name('orders.export');
  });
  Route::middleware(['permission:Просмотр заказов'])->group(function () {
    Route::get('/orders/statistic', [\App\Http\Controllers\Admin\OrderController::class, 'getStatistic'])->name('orders.statistic');
    Route::get('/orders/statistic/form', [\App\Http\Controllers\Admin\OrderController::class, 'getStatisticForm'])->name('orders.statistic.form');
    Route::put('/orders/batchUpdate', [\App\Http\Controllers\Admin\OrderController::class, 'batchUpdate'])->name('orders.batchUpdate');
    Route::post('/orders/{order}/resend-mail', [\App\Http\Controllers\Admin\OrderController::class, 'mailResend'])->name('orders.mailResend');
    Route::post('/orders/{order}/check-status', [\App\Http\Controllers\Admin\OrderController::class, 'checkStatus'])->name('orders.checkStatus');
    Route::middleware(['permission:Комментирование заказов'])->group(function () {
      Route::post('/orders/comment', [\App\Http\Controllers\Admin\OrderController::class, 'order_comment'])->name('orders.order_comment');
    });

    Route::middleware(['permission:Копирование заказов'])->group(function () {
      Route::post('/orders/{order}/copy', [\App\Http\Controllers\Admin\OrderController::class, 'copy'])->name('orders.order_copy');
    });
    Route::middleware(['permission:Управление корзиной'])->group(function () {
      Route::get('/orders/{order}/edit/cart', [\App\Http\Controllers\Admin\OrderController::class, 'editCart'])->name('orders.editCart');
      Route::put('/orders/{order}/edit/cart', [\App\Http\Controllers\Admin\OrderController::class, 'updateCart'])->name('orders.updateCart');
    });
    Route::resource('/orders', '\App\Http\Controllers\Admin\OrderController')->parameters([
        'order' => 'order:slug',
    ]);
  });

  Route::middleware(['permission:Этикетки ШК'])->group(function () {
    Route::get('/tickets', [\App\Http\Controllers\Admin\TicketController::class, 'index'])->name('tickets.index');
    Route::put('/tickets', [\App\Http\Controllers\Admin\TicketController::class, 'batchUpdate'])->name('tickets.batchUpdate');
    Route::get('/tickets/{ticket}', [\App\Http\Controllers\Admin\TicketController::class, 'ticket_split'])->name('tickets.ticket_split');
    Route::post('/orders/getTickets', [\App\Http\Controllers\Admin\OrderController::class, 'requestTickets'])->name('requestTickets');
    Route::post('/tickets/{ticket}/comment', [\App\Http\Controllers\Admin\TicketController::class, 'ticket_comment'])->name('tickets.comment');
    Route::get('/tickets/invoices', [\App\Http\Controllers\Admin\TicketController::class, 'invoice_orders'])->name('tickets.invoice_orders');
    Route::get('/tickets/invoice/group', [\App\Http\Controllers\Admin\TicketController::class, 'ticketsGroups'])->name('tickets.ticketsGroups');
    Route::get('/tickets/invoice/{ticket}', [\App\Http\Controllers\Admin\TicketController::class, 'invoice'])->name('tickets.invoice');
  });
  Route::middleware(['permission:Просмотр накладных'])->group(function () {
    Route::get('/invoices', [\App\Http\Controllers\Admin\InvoiceController::class, 'index'])->name('invoices.index');

    Route::middleware(['permission:Создание накладных'])->group(function () {
      Route::get('/invoices/create', [\App\Http\Controllers\Admin\InvoiceController::class, 'create'])->name('invoices.create');
      Route::post('/invoices/store', [\App\Http\Controllers\Admin\InvoiceController::class, 'store'])->name('invoices.store');
    });
    Route::get('/invoices/{invoice}', [\App\Http\Controllers\Admin\InvoiceController::class, 'show'])->name('invoices.show');
    Route::middleware(['permission:Создание накладных'])->group(function () {
      Route::get('/invoices/{invoice}/edit', [\App\Http\Controllers\Admin\InvoiceController::class, 'edit'])->name('invoices.edit');
      Route::put('/invoices/{invoice}', [\App\Http\Controllers\Admin\InvoiceController::class, 'update'])->name('invoices.update');
    });
  });

  Route::middleware(['permission:Управление настройками'])->group(function () {
    Route::get('/settings', [\App\Http\Controllers\Admin\SettingsController::class, 'settings'])->name('settings');
    Route::post('/settings', [\App\Http\Controllers\Admin\SettingsController::class, 'save'])->name('settings.save');
    Route::get('/settings/info', [\App\Http\Controllers\Admin\SettingsController::class, 'info'])->name('settings.info');
    Route::resource('/sytstem_settings', '\App\Http\Controllers\Admin\SettingsController', ['except' => ['show']])->parameters([
        'setting'
    ]);
  });
  Route::middleware(['permission:Лог действий'])->group(function () {
    Route::get('/log', [\App\Http\Controllers\Admin\LogCongroller::class, 'index'])->name('log.index');
    Route::get('/log/{activity_log}', [\App\Http\Controllers\Admin\LogCongroller::class, 'show'])->name('log.show');
  });
  Route::middleware(['permission:Пользователи'])->group(function () {
    Route::middleware(['permission:Авторизация под другим пользователем'])->post('/users/auth/{user}', [\App\Http\Controllers\Admin\UserController::class, 'auth'])->name('users.auth');
    Route::middleware(['permission:Управление подключением по API'])->post('/users/{user}/createApiToken', [\App\Http\Controllers\Admin\UserController::class, 'createApiToken'])->name('users.createApiToken');
    Route::post('/users/{user}/bonuses/add', [\App\Http\Controllers\Admin\UserController::class, 'addBonuses'])->name('users.bonuses.add');
    Route::post('/users/{user}/bonuses/sub', [\App\Http\Controllers\Admin\UserController::class, 'subBonuses'])->name('users.bonuses.sub');

    Route::resource('/users', '\App\Http\Controllers\Admin\UserController', ['except' => ['show']]);
  });
  Route::post('/regions', [\App\Http\Controllers\Admin\UserController::class, 'regions'])->name('users.regions');
  Route::post('/cities', [\App\Http\Controllers\Admin\UserController::class, 'cities'])->name('users.cities');
  Route::middleware(['permission:Выгрузка пользователей'])->group(function () {
//    Route::get('/users/export', [\App\Http\Controllers\Admin\UserController::class, 'export_page'])->name('users.export_page');
    Route::post('/users/export', [\App\Http\Controllers\Admin\UserController::class, 'export'])->name('users.export');
  });
  Route::middleware(['permission:Выгрузка прав пользователей'])->group(function () {
    Route::post('/users/export-permissions', [\App\Http\Controllers\Admin\UserController::class, 'exportPermissions'])->name('users.export-permissions');
  });
  Route::middleware(['permission:Уведомления о поступлении'])->group(function () {
    Route::get('/products/notifications', [\App\Http\Controllers\Admin\Goods\ProductNotificationController::class, 'index'])->name('products.notifications');
  });
  Route::middleware(['permission:Управление отзывами'])->group(function () {
    Route::get('/products/reviews', [\App\Http\Controllers\Admin\Goods\ReviewController::class, 'index'])->name('products.reviews');
    Route::put('/products/reviews', [\App\Http\Controllers\Admin\Goods\ReviewController::class, 'update'])->name('products.reviews.update');
    Route::put('/products/reviews/{comment}/image/{index}', [\App\Http\Controllers\Admin\Goods\ReviewController::class, 'image'])->name('products.reviews.image');
    Route::post('/products/answer', [\App\Http\Controllers\Admin\Goods\ReviewController::class, 'answer'])->name('products.reviews.answer');
  });
  Route::middleware(['permission:Выгрузка продуктов'])->group(function () {
    Route::post('/products/export', [\App\Http\Controllers\Admin\Goods\ProductController::class, 'export'])->name('products.export');
  });
  Route::middleware(['permission:Просмотр остатков'])->group(function () {
    Route::get('/products/statistic', [\App\Http\Controllers\Admin\Goods\QuantityController::class, 'statistic'])->name('products.statistic');
    Route::get('/products/statistic/get', [\App\Http\Controllers\Admin\Goods\QuantityController::class, 'getStatistic'])->name('products.statistic.get');
  });
  Route::middleware(['permission:Наличие продуктов'])->group(function () {
    Route::get('/products/quantity', [\App\Http\Controllers\Admin\Goods\QuantityController::class, 'index'])->name('products.quantity');
    Route::put('/products/quantity', [\App\Http\Controllers\Admin\Goods\QuantityController::class, 'update'])->name('products.quantity.update');
  });
  Route::middleware(['permission:Просмотр данных для маркетплейсов'])->group(function () {
    Route::get('/products/marketplaces', [\App\Http\Controllers\Admin\Goods\ProductController::class, 'marketplaces'])->name('products.marketplaces');
    Route::put('/products/marketplaces', [\App\Http\Controllers\Admin\Goods\ProductController::class, 'marketplacesSave'])->name('products.marketplaces.put');
  });
  Route::middleware(['permission:Просмотр товаров'])->group(function () {
    Route::get('/products/{product}/edit/design', [\App\Http\Controllers\Admin\Goods\ProductController::class, 'editDesign'])->name('products.editDesign');
    Route::put('/products/{product}/edit/design', [\App\Http\Controllers\Admin\Goods\ProductController::class, 'updateDesign'])->name('products.updateDesign');
    Route::resource('/products', '\App\Http\Controllers\Admin\Goods\ProductController', ['except' => ['show']])->parameters([
        'product' => 'product:slug',
    ]);
  });
  Route::middleware(['permission:Категории товаров'])->group(function () {
    Route::resource('/categories', '\App\Http\Controllers\Admin\Goods\CategoryController', ['except' => ['show']])->parameters([
        'category' => 'category:slug',
    ]);
  });
  Route::middleware(['permission:Типы товаров'])->group(function () {
    Route::resource('/product_types', '\App\Http\Controllers\Admin\Goods\ProductsTypeController', ['except' => ['show']]);
  });
  Route::middleware(['permission:Управление рассылками'])->group(function () {
    Route::post('/mailing_lists/add_users', [\App\Http\Controllers\Admin\MailingListController::class, 'add_users'])->name('mailing_lists.add_users');
    Route::resource('/mailing_lists', '\App\Http\Controllers\Admin\MailingListController', ['except' => ['show']]);
  });
});
