<?php
#!/usr/bin/php
/**
 * // Run 'crontab -e' without quotes and add the next line at the end
 * // * * * * * cd /../Schedule.php && php artisan schedule:run >> /dev/null 2>&1
 *  @URL    https://packagist.org/packages/peppeocchi/php-cron-scheduler
 *
 * Usage:
 *
 * Schedule a php script
 * $scheduler->php('path/to/my/script.php');
 *
 * Schedule a raw command
 * $scheduler->raw('ps aux | grep httpd');
 *
 * Schedule a function
 * $scheduler->call(function () {
 *     return true;
 * });
 *
 * Schedules execution time
 * There are a few methods to help you set the execution time of your schedules. If you don't call any of this method, the job will run every minute (* * * * *).
 *
 * at - This method accepts any expression supported by mtdowling/cron-expression
 * $scheduler->php('script.php')->at('* * * * *');
 *
 * everyMinute - Run every minute
 * $scheduler->php('script.php')->everyMinute();
 *
 * hourly - Run once per hour. You can optionally pass the $minute you want to run, by default it will run every hour at minute '00'.
 * $scheduler->php('script.php')->hourly();
 * $scheduler->php('script.php')->hourly(53);
 *
 * daily - Run once per day. You can optionally pass $hour and $minute to have more granular control (or a string hour:minute)
 * $scheduler->php('script.php')->daily();
 * $scheduler->php('script.php')->daily(22, 03);
 * $scheduler->php('script.php')->daily('22:03');
 *
 * There are additional helpers for weekdays (all accepting optionals hour and minute - defaulted at 00:00)
 * - sunday
 * - monday
 * - tuesday
 * - wednesday
 * - thursday
 * - friday
 * - saturday
 * $scheduler->php('script.php')->saturday();
 * $scheduler->php('script.php')->friday(18);
 * $scheduler->php('script.php')->sunday(12, 30);
 *
 * And additional helpers for months (all accepting optionals day, hour and minute - defaulted to the 1st of the month at 00:00)
 * - january
 * - february
 * - march
 * - april
 * - may
 * - june
 * - july
 * - august
 * - september
 * - october
 * - november
 * - december
 * $scheduler->php('script.php')->january();
 * $scheduler->php('script.php')->december(25);
 * $scheduler->php('script.php')->august(15, 20, 30);
 *
 * And manually setting date/time
 * $scheduler->php('script.php')->date('2018-01-01 12:20');
 * $scheduler->php('script.php')->date(new DateTime('2018-01-01'));
 * $scheduler->php('script.php')->date(DateTime::createFromFormat('!d/m Y', '01/01 2018'));
 *
 * Send output to file/s
 * $scheduler->php('script.php')->output([
 *     'my_file1.log', 'my_file2.log'
 * ]);
 * $scheduler->call(function () {
 *     echo "Hello";
 *     return " world!";
 * })->output('my_file.log');
 *
 * Send output to email/s
 * $scheduler->php('script.php')->output([
 *     // If you specify multiple files, both will be attached to the email
 *     'my_file1.log', 'my_file2.log'
 * ])->email([
 *     'someemail@mail.com' => 'My custom name',
 *     'someotheremail@mail.com'
 * ]);
 *
 * Schedule conditional execution
 * $scheduler->php('script.php')->when(function () {
 *     // The job will run (if due) only when
 *     // this function returns true
 *     return true;
 * });
 *
 * Before job execution
 * // $logger here is your own implementation
 * $scheduler->php('script.php')->before(function () use ($logger) {
 *     $logger->info("script.php started at ".time());
 * });
 *
 * After job execution
 * // $logger and $messenger here are your own implementation
 * $scheduler->php('script.php')->then(function ($output) use ($logger, $messenger) {
 *     $logger->info($output);
 *     $messenger->ping('myurl.com', $output);
 * });
 * $scheduler->php('script.php')->then(function ($output) use ($logger) {
 *     $logger->info('Job executed!');
 * }, true);
 *
 * Using "before" and "then" together
 * // $logger here is your own implementation
 * $scheduler->php('script.php')->before(function () use ($logger) {
 *     $logger->info("script.php started at " . time());
 * })->then(function ($output) use ($logger) {
 *     $logger->info("script.php completed at " . time(), [
 *         'output' => $output,
 *     ]);
 * });
 *
 * Multiple scheduler runs
 * # some code
 * $scheduler->run();
 * # ...
 *
 * // Reset the scheduler after a previous run
 * $scheduler->resetRun()
 *     ->run(); // now we can run it again
 */
require_once __DIR__.'/vendor/autoload.php';

use GO\Scheduler;

class Schedule
{
    public function run()
    {
        $scheduler = new Scheduler();
        // ... configure the scheduled jobs (see below) ...

        if (gethostname() === 'ku') {

            /**
             * Test schedule
             */
            $scheduler->php(__DIR__.'/../jobs/test.php')
                ->output(__DIR__.'/../jobs/test.log');

            /**
             * StrategyBank update via EDFI
             */
            //Entities
            $scheduler->php(__DIR__.'/../jobs/misb/1_edfi_update_entities.php')
                ->daily(10, 00) // 10:00am
                ->output(__DIR__ . '/../jobs/misb/log/entities.log');

            //School Years & sets current school year
            $scheduler->php(__DIR__.'/../jobs/misb/1_edfi_update_school_years.php')
                ->daily(10, 05) // 10:05am
                ->output(__DIR__.'/../jobs/misb/log/school_years.log');

            //Staff & affiliations
            $scheduler->php(__DIR__.'/../jobs/misb/2_edfi_update_staff_v2.php')
                ->daily(10, 10) // 10:10am
                ->output(__DIR__.'/../jobs/misb/log/staff.log');

            //Students & affiliations
            $scheduler->php(__DIR__.'/../jobs/misb/2_edfi_update_students_v2.php')
                ->daily(10, 30) // 10:30am
                ->output(__DIR__.'/../jobs/misb/log/students.log');
        }

        // Let the scheduler execute jobs which are due.
        $scheduler->run();
    }
}

Schedule::run();
