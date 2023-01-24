<?php

namespace App\Commands;

use LaravelZero\Framework\Commands\Command;

class ReviewSummaryCommand extends Command
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'review:summary';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Summary list of review';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
      if(!\Cache::has('summary')) {
        $string = file_get_contents("./database/reviews.json");
        $json_file = json_decode($string, true);
        $save = array(
          'total_reviews' => count($json_file),
          'average_ratings' => 0,
          '5_star' => 0,
          '4_star' => 0,
          '3_star' => 0,
          '2_star' => 0,
          '1_star' => 0,
        );
        foreach ($json_file as $key => $value) {
          $save[$value['rating'].'_star'] += 1;
          $save['average_ratings'] += $value['rating'];
        }

        $save['average_ratings'] /= $save['total_reviews'];
        $result = collect($save)->toJson();
        \Cache::put('summary', $result, now()->addMinutes(2));
      }

      $this->info(\Cache::get('summary'));
    }
}
