<?php
function getSettings($key)
{
  return \App\Models\Setting::dontCache()->where('key', $key)->first()?->value;
}
