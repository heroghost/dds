<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */


namespace App\Http\Controllers;



class SymptomController extends Controller
{
    public static $disease = [];
    public static $symptom = [];
    /**
     * 显示指定用户的个人数据。
     *
     * @param  int  $id
     * @return Response
     */
    public function getSymptomListByPart()
    { 
        $firstPart = urldecode($_POST['first_part']);
        $secondPart = '';
        $sex = '';
        $sql = "Select * from `prepare2032_copy` where 部位=? and 典型非典型=?";
        $params = [$firstPart, 0];
        if(array_key_exists('second_part', $_POST)) {
            $secondPart = urldecode($_POST['second_part']);
            $sql .= ' and 二级细分部位=?';
            $params[] = $secondPart;
        } else {
        }
        if(array_key_exists('sex', $_POST)) {
            $sex = urldecode($_POST['sex']);
            $sql .= ' and (群体=2 or 群体=?)';
            $params[] = $sex;
        } else {
            $sql .= ' and 群体=2';
        }
        
            
        $partArr = \DB::select($sql, $params);
        $symptomNameArr = [];
        
        foreach($partArr as $part) {
            if(!$part->症状) {
                continue;
            }
            if(!in_array($part->症状, $symptomNameArr)) {
                $symptomNameArr[] = $part->症状;
            }
        }
        return $symptomNameArr;
    }
    
    //这里的数据需要排序，例如（部位）(二级部位)都需要排序，现在的排序太乱，在数据库里增加（排序字段）。
    public function getSubSymptomList() {
        $firstSymptom = urldecode($_POST['first_symptom']);
        $partArr = \DB::select("SELECT * FROM `Symptomtosubdivide` where 一级症状=?",[$firstSymptom]);
        $symptomNameArr = [];

        foreach($partArr as $part) {
            $part = $this->ObjectToArray($part);
            for($i=1;$i<100;$i++) {
                if(!array_key_exists('典型非典型'.$i, $part)) {
                    break;
                }
                if($part['典型非典型'.$i] != 0) {
                    continue;
                }
                if(!$part['二级症状'.$i]) {
                    continue;
                }
                $symptomNameArr[] = $part['二级症状'.$i];
            }
        }

        return $symptomNameArr;
    }
    
    public function loadSymptomList() {
        $firstPart = $this->postParam('first_part');
        $firstSymptom = $this->postParam('first_symptom');
        $subDivideSymptom = $this->postParam('sub_symptom');
        $sex = $this->postParam('sex');
        $length = $this->postParam('length');
        $weight = $this->postParam('weight');
        $highPressure = $this->postParam('high_pressure');
        $lowPressure = $this->postParam('low_pressure');
        $temperature = $this->postParam('temperature');
        $rhythm = $this->postParam('rhythm');
        $pulse = $this->postParam('pulse');
        $temperature = $this->postParam('temperature');
        $FastingBloodGlucose = $this->postParam('fasting_blood_glucose');
        
    }
    
    public function reloadSymptomList() {
        $firstPart = urldecode($_POST['first_part']);
        $secondPart = '';
        $sex = '';
        if(array_key_exists('second_part', $_POST)) {
            $secondPart = urldecode($_POST['second_part']);
        } else {
        }
        if(array_key_exists('sex', $_POST)) {
            $sex = urldecode($_POST['sex']);
        }
        $firstSymptom = urldecode($_POST['first_symptom']);
        
        $this->loadSymptoms();
    }
    
    private function getBasicDisease() {
        //
    }
    
    private function bmiDisease($height, $weight) {
        if(!$height || !$weight) {
            return false;
        }
        $bmi = $weight / pow(($height / 100), 2);        
        if($bmi < 18.5 && $bmi >0) {
            $symptomName = '体重下降';
        } else if($bmi > 28) {
            $symptomName = '体重增加';
        }
        if(!static::$symptom) {
            $this->loadSymptoms();
        }
        return static::$symptom[$symptomName]['disease'];
    }
    
    private function pressureDisease($highPressure, $lowPressure) {
        if(!$highPressure || !$lowPressure) {
            return false;
        }
        if($highPressure > 140 || $lowPressure > 90) {
            $symptomName = '血压升高';
        } else if($highPressure < 90 || $lowPressure < 60) {
            $symptomName = '血压下降';
        }
        if(!static::$symptom) {
            $this->loadSymptoms();
        }
        return static::$symptom[$symptomName]['disease'];
    }
    
    private function temperatureDisease($temperature) {
        if(!$temperature) {
            return false;
        }
        if($temperature > 37) {
            $symptomName = '发热';
        } 
        if(!static::$symptom) {
            $this->loadSymptoms();
        }
        return static::$symptom[$symptomName]['disease'];
    }
    
    private function rhythmDisease($rhythm) {
        if(!$rhythm) {
            return false;
        }
        if($rhythm < 50) {
            $symptomName = '心率缓慢';
        } else if($rhythm > 80) {
            $symptomName = '心率加快';
        }
        if(!static::$symptom) {
            $this->loadSymptoms();
        }
        return static::$symptom[$symptomName]['disease'];
    }
    
    private function pulseDisease($pulse) {
        if(!$pulse) {
            return false;
        }
        if($pulse > 100) {
            $symptomName = '脉搏细速';
        } else if($pulse < 60) {
            $symptomName = '脉搏下降';
        }
        if(!static::$symptom) {
            $this->loadSymptoms();
        }
        return static::$symptom[$symptomName]['disease'];
    }
    
    private function bloodGlucoseDisease($bloodGlucose) {
        if(!$bloodGlucose) {
            return false;
        }
        if($bloodGlucose > 6.11) {
            $symptomName = '血糖升高';
        }
        if(!static::$symptom) {
            $this->loadSymptoms();
        }
        return static::$symptom[$symptomName]['disease'];
    }
    
    
    private function loadDisease() {
        
        $diseaseSource = \DB::select("SELECT * FROM `book_diseasedatabased_946`");
        
        foreach($diseaseSource as $disease) {
            $diseaseArr = [];
            foreach($disease as $k=>$v) {
                $diseaseArr[$k] = $v;
            }
            static::$disease[$disease->病种] = [
                'disease'=>[
                    'name'=>$diseaseArr['病种'],
                    'cat1'=>$diseaseArr['粗分类名称1'],
                    'cat1'=>$diseaseArr['粗分类名称2'],
                    'id'=>$diseaseArr['id'],
                    'department'=>$diseaseArr['病情科室'],
                    'usertype'=>$diseaseArr['空白1（群体专属，0表示男，1表示女，2表示通常，3表示老人，4表示儿童，5表示婴幼儿）'],
                    'reason'=>$diseaseArr['空白2（诱因1）'],
                    'iscommon'=>$diseaseArr['空白3（是否常见病并赋值）'],
                    'part'=>$diseaseArr['空白4(疾病对应部位)'],
                    'state'=>$diseaseArr['空白5()']
                ],
                'symptom'=>[]
            ];
            for($i=1;$i<200;$i++) {
                if(!array_key_exists('症状'.$i, $diseaseArr)) {
                    break;
                }
                static::$disease[$disease->病种]['symptom'][] = [
                    'name' => $diseaseArr['症状'.$i],
                    'part' => $diseaseArr['部位'.$i],
                    'istypical' => $diseaseArr['典型或非典型'.$i],
                    'reason' => $diseaseArr['症状解释'.$i],
                    'method' => $diseaseArr['症状方案'.$i],
                    'subsymptom' => $diseaseArr['（症）空白（症状二级细分）'.$i],
                    'subpart' => $diseaseArr['（症）空白（部位二级细分）'.$i],
                    'empty_a' => $diseaseArr['（症）空白（a）'.$i],
                    'empty_b' => $diseaseArr['（症）空白（b）'.$i],
                    'empty_c' => 1//$diseaseArr['（症）空白（c）'.$i],
                ]; 
            }
        }
    }
    
    private function loadSymptoms() {
        if(!static::$disease) {
            $this->loadDisease();
        }
        foreach(static::$disease as $disease) {
            foreach($disease['symptom'] as $symptom) {
                if(!in_array($symptom['name'], static::$symptom)) {
                    static::$symptom[$symptom['name']] = [
                        'symptom'=>$symptom,
                        'disease'=>[]
                    ];               
                }
                static::$symptom[$symptom['name']]['disease'][] = $disease['disease']['name'];
                
            }
            
        }
    }
}




























