<?php

namespace App\Commands;

use LaravelZero\Framework\Commands\Command;

class ReviewProductCommand extends Command
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'review:product {productId : Id for product}';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'List review of product';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {

      $productId = $this->argument('productId');

      $cacheKey = "summray_product".$productId;

      if(!\Cache::has($cacheKey)) {
        $string = file_get_contents("./database/reviews.json");
        $json_file = json_decode($string, true);
        $save = array(
          'total_reviews' => 0,
          'average_ratings' => 0,
          '5_star' => 0,
          '4_star' => 0,
          '3_star' => 0,
          '2_star' => 0,
          '1_star' => 0,
        );
        foreach ($json_file as $key => $value) {
          if ($value['product_id'] == $productId) {
            $save['total_reviews'] += 1;
            $save[$value['rating'].'_star'] += 1;
            $save['average_ratings'] += $value['rating'];
          }
        }

        $save['average_ratings'] /= $save['total_reviews'];
        $result = collect($save)->toJson();
        \Cache::put($cacheKey, $result, now()->addMinutes(2));
      }
      $this->info(\Cache::get($cacheKey));
    }
}
