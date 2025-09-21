<?php
namespace RealSoft\FEL\Contingency\GT;

use Illuminate\Support\Facades\DB;

class AccessNumberGenerator
{
    public function __construct(protected int $digits = 18) {}

    public function next(string $establishment): string
    {
        return DB::transaction(function() use ($establishment){
            $row = DB::table('fel_access_sequences')->lockForUpdate()->where([
                'country' => 'GT', 'establishment_code' => $establishment
            ])->first();

            if (!$row) {
                DB::table('fel_access_sequences')->insert([
                    'country'=>'GT','establishment_code'=>$establishment,'current_value'=>0,'digits'=>$this->digits,'updated_at'=>now()
                ]);
                $row = DB::table('fel_access_sequences')->where([
                    'country'=>'GT','establishment_code'=>$establishment
                ])->first();
            }

            $next = $row->current_value + 1;
            DB::table('fel_access_sequences')->where('id',$row->id)->update(['current_value'=>$next,'updated_at'=>now()]);

            return str_pad((string)$next, $row->digits, '0', STR_PAD_LEFT);
        });
    }
}
