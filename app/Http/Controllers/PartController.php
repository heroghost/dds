<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */


namespace App\Http\Controllers;



class PartController extends Controller
{
    /**
     * 显示指定用户的个人数据。
     *
     * @param  int  $id
     * @return Response
     */
    public function getPartList()
    { 
        $partArr = [
            "全身",
            "皮肤", 
            "头颈", 
            "五官", 
            "四肢及躯干",
            "心脏",
            "胸肺",
            "腰腹", 
            "消化",
            "神经",
            "泌尿及生殖"
            ];
        return $partArr;
    }
    
    //这里的数据需要排序，例如（部位）(二级部位)都需要排序，现在的排序太乱，在数据库里增加（排序字段）。
    public function getSubPartList() {
        $part = $_POST['part'];
        $part = urldecode($part);
        $partArr = \DB::select("SELECT * FROM `prepare2032_copy` where 部位=? and 典型非典型=?",[$part,0]);
        $partNameArr = [];
        
        foreach($partArr as $part) {
            if(!$part->二级细分部位 || in_array($part->二级细分部位, $partNameArr)) {
                continue;
            }
            $partNameArr[] = $part->二级细分部位;
        }
        return $partNameArr;
    }
}