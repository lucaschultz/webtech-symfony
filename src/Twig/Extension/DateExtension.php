<?php

namespace App\Twig\Extension;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use DateTimeInterface;

class DateExtension extends AbstractExtension {
  public function getFilters(): array {
    return [new TwigFilter("ago", [$this, "formatAgo"])];
  }

  public function formatAgo(DateTimeInterface $date): string {
    $now = new \DateTime();
    $diff = $now->diff($date);

    if ($diff->invert === 0) {
      if ($diff->y > 0) {
        return $diff->y .
          " " .
          ($diff->y === 1 ? "year" : "years") .
          " from now";
      }
      if ($diff->m > 0) {
        return $diff->m .
          " " .
          ($diff->m === 1 ? "month" : "months") .
          " from now";
      }
      if ($diff->d > 0) {
        return $diff->d . " " . ($diff->d === 1 ? "day" : "days") . " from now";
      }
      if ($diff->h > 0) {
        return $diff->h .
          " " .
          ($diff->h === 1 ? "hour" : "hours") .
          " from now";
      }
      if ($diff->i > 0) {
        return $diff->i .
          " " .
          ($diff->i === 1 ? "minute" : "minutes") .
          " from now";
      }
      return "just now";
    } else {
      if ($diff->y > 0) {
        return $diff->y . " " . ($diff->y === 1 ? "year" : "years") . " ago";
      }
      if ($diff->m > 0) {
        return $diff->m . " " . ($diff->m === 1 ? "month" : "months") . " ago";
      }
      if ($diff->d > 0) {
        return $diff->d . " " . ($diff->d === 1 ? "day" : "days") . " ago";
      }
      if ($diff->h > 0) {
        return $diff->h . " " . ($diff->h === 1 ? "hour" : "hours") . " ago";
      }
      if ($diff->i > 0) {
        return $diff->i .
          " " .
          ($diff->i === 1 ? "minute" : "minutes") .
          " ago";
      }
      return "just now";
    }
  }
}
