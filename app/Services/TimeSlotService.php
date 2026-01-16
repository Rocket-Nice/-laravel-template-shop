<?php

namespace App\Services;

use Carbon\Carbon;
use InvalidArgumentException;

class TimeSlotService
{
  public function getRandomAvailableTime(array $timeRange, array $bookedSlots = []): string
  {
    // Validate time range format
    if (!isset($timeRange[0]) || !isset($timeRange[1])) {
      throw new InvalidArgumentException('Time range must contain start and end times');
    }

    // Convert string times to Carbon instances
    $startTime = Carbon::createFromFormat('H:i', $timeRange[0]);
    $endTime = Carbon::createFromFormat('H:i', $timeRange[1]);

    // Convert booked slots to Carbon instances
    $bookedTimes = collect($bookedSlots)->map(function ($time) {
      return Carbon::createFromFormat('H:i', $time);
    })->sort();

    // Generate random time until finding an available slot
    $maxAttempts = 100;
    $attempt = 0;

    while ($attempt < $maxAttempts) {
      // Generate random minutes between start and end time
      $diffInMinutes = $endTime->diffInMinutes($startTime);
      $randomMinutes = rand(0, $diffInMinutes);

      $randomTime = (clone $startTime)->addMinutes($randomMinutes);

      // Check if time is available
      if (!$this->isTimeBooked($randomTime, $bookedTimes)) {
        return $randomTime->format('H:i');
      }

      $attempt++;
    }

    throw new \RuntimeException('Could not find available time slot');
  }

  private function isTimeBooked(Carbon $time, $bookedTimes): bool
  {
    return $bookedTimes->contains(function ($bookedTime) use ($time) {
      return $bookedTime->format('H:i') === $time->format('H:i');
    });
  }
}
