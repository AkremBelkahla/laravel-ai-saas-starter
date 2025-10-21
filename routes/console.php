<?php

use Illuminate\Support\Facades\Schedule;

Schedule::command('app:reset-credits')->monthlyOn(1, '00:00');
