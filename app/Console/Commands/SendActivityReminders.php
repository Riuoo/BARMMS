<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\HealthCenterActivity;
use App\Models\AccomplishedProject;
use App\Services\ActivityAudienceService;
use App\Mail\HealthActivityNotificationMail;
use App\Mail\BarangayActivityNotificationMail;
use Illuminate\Support\Facades\Mail;

class SendActivityReminders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'activities:send-reminders';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send reminder emails for upcoming health and barangay activities';

    public function handle(ActivityAudienceService $audienceService): int
    {
        $tomorrow = now()->addDay()->toDateString();

        // Health activities
        $healthActivities = HealthCenterActivity::whereDate('activity_date', $tomorrow)
            ->where('reminder_sent', false)
            ->get();

        foreach ($healthActivities as $activity) {
            $this->sendReminderForHealthActivity($activity, $audienceService);
        }

        // Barangay activities (accomplished projects of type activity)
        $barangayActivities = AccomplishedProject::where('type', 'activity')
            ->whereDate('completion_date', $tomorrow)
            ->where('reminder_sent', false)
            ->get();

        foreach ($barangayActivities as $activity) {
            $this->sendReminderForBarangayActivity($activity, $audienceService);
        }

        return static::SUCCESS;
    }

    protected function sendReminderForHealthActivity(HealthCenterActivity $activity, ActivityAudienceService $audienceService): void
    {
        $residents = $audienceService->getAudienceResidents(
            $activity->audience_scope ?? 'all',
            $activity->audience_purok
        );

        if ($residents->isEmpty()) {
            $activity->reminder_sent = true;
            $activity->save();
            return;
        }

        foreach ($residents as $resident) {
            Mail::to($resident->email)->queue(new HealthActivityNotificationMail($activity));
        }

        $activity->reminder_sent = true;
        $activity->save();
    }

    protected function sendReminderForBarangayActivity(AccomplishedProject $activity, ActivityAudienceService $audienceService): void
    {
        $residents = $audienceService->getAudienceResidents(
            $activity->audience_scope ?? 'all',
            $activity->audience_purok
        );

        if ($residents->isEmpty()) {
            $activity->reminder_sent = true;
            $activity->save();
            return;
        }

        foreach ($residents as $resident) {
            Mail::to($resident->email)->queue(new BarangayActivityNotificationMail($activity));
        }

        $activity->reminder_sent = true;
        $activity->save();
    }
}


