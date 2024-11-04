<?php

namespace App\Http\Controllers\Affliate;

use App\Models\Rank;
use App\Models\User;
use App\Models\GlobelPoll;
use Illuminate\Http\Request;
use App\Models\UserRanksRelation;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Http;
use App\Models\UsersGenerationRelation;

class RankControllr extends Controller
{
    public function Create_userRank($userId,$comming)
    {
        // return $comming;

               $getRankRecord = UserRanksRelation::where('user_id', $userId)->first();
            if (!$getRankRecord) {
                $newRecord = new UserRanksRelation;
                $newRecord->user_id = $userId;
                $newRecord->rank_id = 1;
                $newRecord->save();
            }

            $getGenration=UsersGenerationRelation::where('user_id_child',$userId)->first();

            if(!$getGenration)
            {
               $this->getAllFathersAndCreateGenerationRelations($userId,$comming);

            }




        }
        
    public function checkRank($userId)
    {

         Http::post('https://fahd.gfoura.smartidea.tech/api/test-afflite', [
            'user_id' => $userId,
            // 'coming_affiliate'=>$userCreated->comming_affiliate
        ]);
    }



    public function getAllFathersAndCreateGenerationRelations($userId,$comming)
         {
        set_time_limit(300);

        $coming_affiliate = $comming;


        $childId = $userId;
        $fathers = [];
        $currentAffiliateCode = $coming_affiliate;
        $i = 1;
        while (true) {
            $father = User::where('affiliate_code', $currentAffiliateCode)->first();

            if (!$father) {
                break;
            }
            $fathers[] = $father;
            $generationNumber = $i++;
            $currentAffiliateCode = $father->comming_afflite;
            // if ($this->CheckIfHeHasSameFatherAndSameGeneration($childId, $father->id, $generationNumber)) {
            $this->createUsersGenerationRelation($childId, $father->id, $generationNumber);
              $this->calculationFatherChild($father->id);
            // }
            if ($father->affiliate_code == $father->coming_affiliate) {
                break;
            }
        }
        // throw new HttpResponseException(response()->json(['fathers' => $fathers], 400));
    }




    public function createUsersGenerationRelation($childId, $fatherId, $generationNumber)
    {

        UsersGenerationRelation::create([
            'user_id_child' => $childId,
            'user_id_father' => $fatherId,
            'free_check' => 0,
            'generation_id' => $generationNumber,
        ]);
    }

    public function calculationFatherChild($userId)
    {
        $getAllChildInFirstGeneration = count(UsersGenerationRelation::where('user_id_father', $userId)->where('generation_id', 1)->where('free_check', 1)->get());
          $getAllChildInAllGeneration = count(UsersGenerationRelation::where('user_id_father', $userId)->where('free_check', 1)->get());
           $getAllChildInAllGenerationFree = count(UsersGenerationRelation::where('user_id_father', $userId)->where('free_check', 0)->get());
         $getUserRank = UserRanksRelation::where('user_id', $userId)->first();
        $getUserRank->direct_child_number = $getAllChildInFirstGeneration ?? 0;
        $getUserRank->child_number = $getAllChildInAllGeneration ?? 0;
        $getUserRank->child_free = $getAllChildInAllGenerationFree ?? 0;
         $this->checkHisRank($getUserRank);
        $getUserRank->save();
    }

    public function checkHisRank($userRank)
    {
           $userRankDirectChild = $userRank['direct_child_number'];
             $userRankAllChild = $userRank['child_number'];
                $ranks = Rank::get();
        foreach ($ranks as $rank) {
            if($userRankAllChild == $rank->max_number)
             {
                if ($userRankDirectChild >= $rank->max_direct_number) {
                    $userRank->rank_id = $rank->id;

                    $allHavingGlobelPoll = GlobelPoll::get();
                    foreach ($allHavingGlobelPoll as $poll) {
                        if ($poll->percentage_rank == $rank->id) {
                            $userRank->globel_percentage = $poll->id;
                        }
                    }
                    $userRank->block_generation = $rank->id + 1;
                    $userRank->save();
                }
            }
        }
    }

}
