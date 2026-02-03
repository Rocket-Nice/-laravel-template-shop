<?php

namespace App\Services;

class DashamailService
{
  protected PHPDasha $client;
  protected string $listId;
  protected string $secondaryList;
  protected bool $enabled = false;

  public function __construct()
  {
    $username = (string) config('services.dashamail.username');
    $password = (string) config('services.dashamail.password');
    $listId = (string) config('services.dashamail.list_id_main');

    $this->client = new PHPDasha(
        $username,
        $password
    );

    $this->listId = $listId;
    $this->enabled = $username !== '' && $password !== '' && $listId !== '';
  }

  /**
   * Проверка, существует ли email в базе, и получение его статуса
   *
   * @param string $email
   * @return array|null
   */
  public function getEmailStatus(string $email): ?array
  {
    if (!$this->enabled) {
      return null;
    }

    $members = $this->client->lists_get_members($this->listId, [
        'email' => $email,
    ]);
    if (!is_array($members) || empty($members)) {
      return null;
    }

    foreach ($members as $member) {
      if (isset($member['email']) && strtolower($member['email']) === strtolower($email)) {
        return $member;
      }
    }

    return null;
  }

  /**
   * Добавление email в базу
   *
   * @param string $email
   * @param array $params
   * @return bool|string
   */
  public function addEmail(string $email, array $params = []): bool|string
  {
    if (!$this->enabled) {
      return true;
    }

    $result = $this->client->lists_add_member($this->listId, $email, $params);

    return is_array($result) ? true : $result; // Возвращает true или текст ошибки
  }

  /**
   * Удаление email из базы
   */

  public function deleteEmail(string $email)
  {
    if (!$this->enabled) {
      return true;
    }

    $results = [];

    // Удаляем из основного списка
    $memberMain = $this->getMemberByList($email, $this->listId);
    if ($memberMain && isset($memberMain['id'])) {
      $result = $this->client->lists_delete_member($memberMain['id']);
      return $result;
    }

//    // Удаляем из дополнительного списка
//    $memberSecondary = $this->getMemberByList($email, $this->listId);
//    if ($memberSecondary && isset($memberSecondary['member_id'])) {
//      $result = $this->client->lists_delete_member($memberSecondary['member_id']);
//      $results['secondary'] = $result === true || is_array($result);
//    }

    return !empty($results) ? $results : 'Email не найден ни в одной из баз.';
  }

  /**
   * Получить подписчика по email и ID базы
   */
  private function getMemberByList(string $email, string $listId): ?array
  {
    if (!$this->enabled) {
      return null;
    }

    $members = $this->client->lists_get_members($listId, ['email' => $email]);

    if(is_array($members) && !empty($members)) {
      foreach ($members as $member) {
        if (strtolower($member['email']) === strtolower($email)) {
          return $member;
        }
      }
    }


    return null;
  }

  /**
   * Массовое добавление пользователей в рассылку
   *
   * @param \Illuminate\Support\Collection|\Illuminate\Database\Eloquent\Collection $users
   * @return array
   */
  public function syncUsersToLists($users): array
  {
    if (!$this->enabled) {
      return [];
    }

    $batchMain = [];
    $batchSecondary = [];

    foreach ($users as $user) {
      $emailEntry = [
          'email' => $user->email,
          'state' => 'active',
          'merge_1' => $user->name ?? '',
      ];

      $batchMain[] = $emailEntry;

      if (!$user->is_subscribed_to_marketing) {
        $batchSecondary[] = $emailEntry;
      }
    }

    $results = [];

    if (!empty($batchMain)) {
      $results['main'] = $this->client->lists_add_member_batch($this->listId, $batchMain);
    }

    if (!empty($batchSecondary)) {
      $results['secondary'] = $this->client->lists_add_member_batch($this->secondaryList, $batchSecondary);
    }

    return $results;
  }
}
