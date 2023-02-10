<?php

use Illuminate\Support\Facades\DB;


function userMenu(){
    $mnGroups = DB::table('v_usermenus')
                ->select('menugroup', 'groupname', 'groupicon','group_idx')
                ->distinct()
                ->where('userid', Auth::user()->id)
                ->orderBy('group_idx','ASC')
                ->get();
    return $mnGroups;
}

function userSubMenu(){
    $mnGroups = DB::table('v_usermenus')
                ->select('menugroup', 'route', 'menu_desc','menu_idx', 'icon')
                ->distinct()
                ->where('userid', Auth::user()->id)
                ->orderBy('menu_idx','ASC')
                ->get();
    return $mnGroups;
}

function getLocalDatabaseDateTime(){
    // SELECT now()
    $localDateTime = DB::select('SELECT fGetDatabaseLocalDatetime() as lcldate');
    return $localDateTime[0]->lcldate;
}

function getCurrentTime(){
    $localDateTime = DB::select('SELECT CURRENT_TIME as Curr_time');
    return $localDateTime[0]->Curr_time;
}

function formatDate($date, $format = "d-m-Y")
{
    if (is_null($date)) {
        return '-';
    }
    return date($format, strtotime($date));
}

function formatDateTime($dateTime, $format = "d-m-Y h:i A")
{
    if (is_null($dateTime)) {
        return '-';
    }
    return ($dateTime) ? date($format, strtotime($dateTime)) : $dateTime;
}

function generateIDoutlet($jenis){
    $dcnNumber = '';
    $getdata = DB::table('tc_nriv_toko')->where('jenis_outlet', $jenis)->first();
    // dd($getdata);
    if($getdata){
        DB::beginTransaction();
        try{
            $leadingZero = '';
            if(strlen($getdata->current_number) == 5){
                $leadingZero = '0';
            }elseif(strlen($getdata->current_number) == 4){
                $leadingZero = '00';
            }elseif(strlen($getdata->current_number) == 3){
                $leadingZero = '000';
            }elseif(strlen($getdata->current_number) == 2){
                $leadingZero = '0000';
            }elseif(strlen($getdata->current_number) == 1){
                $leadingZero = '00000';
            }else{
                $leadingZero = $getdata->from_number;
                $getdata->current_number = 0;
            }

            $lastnum = ($getdata->current_number*1) + 1;

            if($leadingZero == ''){
                $dcnNumber = $getdata->prefix . $lastnum; 
            }else{
                $dcnNumber = $getdata->prefix . $leadingZero . $lastnum; 
            }
            // dd($dcnNumber);
            DB::table('tc_nriv_toko')->where('jenis_outlet', $jenis)->update([
                'current_number' => $lastnum
            ]);

            DB::commit();
            return $dcnNumber;
        }catch(\Exception $e){
            DB::rollBack();
            return null;
        }
    }else{
        $getdata = DB::table('tc_nriv_toko')->where('jenis_outlet', $jenis)->first();
        $dcnNumber = $getdata->prefix .'100000';
        DB::beginTransaction();
        try{
            DB::table('dcn_nriv')->insert([
                'year'            => date('Y'),
                'object'          => $doctype,
                'current_number'  => '1',
                'createdon'       => date('Y-m-d H:m:s'),
                'createdby'       => Auth::user()->email ?? Auth::user()->username
            ]);
            DB::commit();
            return $dcnNumber;
        }catch(\Exception $e){
            DB::rollBack();
            return null;
        }
    }
    
}

function generateVisitNumber(){
    $dcnNumber = '';
    $year      = date('Y');
    $getdata = DB::table('t_nriv')->where('object', 'VISIT')->where('nyear', $year)->first();
    // dd($getdata);
    if($getdata){
        DB::beginTransaction();
        try{
            $lastnum   = $getdata->currentnum*1;
            $dcnNumber = $lastnum;
            // dd($dcnNumber);
            DB::table('t_nriv')->where('object', 'VISIT')->where('nyear', $year)->update([
                'currentnum' => $lastnum+1
            ]);

            DB::commit();
            return $dcnNumber;
        }catch(\Exception $e){
            DB::rollBack();
            return null;
        }
    }else{
        
        DB::beginTransaction();
        try{
            DB::table('t_nriv')->insert([
                'object'          => 'VISIT',
                'nyear'           => date('Y'),
                'fromnum'         => '4000000000',
                'tonumber'        => '4999999999',
                'currentnum'      => '4000000000',
                'createdon'       => date('Y-m-d H:m:s'),
                'createdby'       => Auth::user()->email ?? Auth::user()->username
            ]);
            DB::commit();
            $dcnNumber = '4000000000';
            return $dcnNumber;
        }catch(\Exception $e){
            DB::rollBack();
            return null;
        }
    }
    
}

function getWfGroup($doctype){

    $wfgroup = DB::table('doctypes')->where('id', $doctype)->first();
    if($wfgroup){
        return $wfgroup->workflow_group;
    }else{
        return 0;
    }
}

function groupOpen($groupid){
    $routeName = \Route::current()->uri();
    $selectMenu = DB::table('menus')->where('route', $routeName)->first();
    if($selectMenu){
        return $groupid == $selectMenu->menugroup ? 'menu-open' : '';
    }
    // return request()->is("*".$groupname."*") ? 'menu-open' : '';
}

function currentURL(){
    $routeName = \Route::current()->uri();
    $selectMenu = DB::table('menus')->where('route', $routeName)->first();
    if($selectMenu){

    }
    dd(\Route::current()->uri());
}

function active($partialUrl){
    // return $partialUrl;
    return request()->is("*".$partialUrl."*") ? 'active' : '';
}

function insertOrUpdate(array $rows, $table){
    $first = reset($rows);

    $columns = implode(
        ',',
        array_map(function ($value) {
            return "$value";
        }, array_keys($first))
    );

    $values = implode(',', array_map(function ($row) {
            return '('.implode(
                ',',
                array_map(function ($value) {
                    return '"'.str_replace('"', '""', $value).'"';
                }, $row)
            ).')';
    }, $rows));

    $updates = implode(
        ',',
        array_map(function ($value) {
            return "$value = VALUES($value)";
        }, array_keys($first))
    );

    $sql = "INSERT INTO {$table}({$columns}) VALUES {$values} ON DUPLICATE KEY UPDATE {$updates}";

    return \DB::statement($sql);
}

function userAllowDownloadDocument(){
    $checkData = DB::table('user_object_auth')
                ->where('userid', Auth::user()->id)
                ->where('object_name', 'ALLOW_DOWNLOAD_DOC')
                ->first();
    if($checkData){
        if($checkData->object_val === "Y"){
            return 1;
        }else{
            return 0;
        }
    }else{
        return 0;
    }
}

function userAllowChangeDocument(){
    $checkData = DB::table('user_object_auth')
                ->where('userid', Auth::user()->id)
                ->where('object_name', 'ALLOW_CHANGE_DOC')
                ->first();
    if($checkData){
        if($checkData->object_val === "Y"){
            return 1;
        }else{
            return 0;
        }
    }else{
        return 0;
    }
}

function checkIsLocalhost(){
    if(request()->getHost() == "localhost"){
        return 1;
    }else{
        return 0;
    }
}

function getbaseurl(){
    $baseurl = env('APP_URL');
    return $baseurl;
}

function allowUplodOrginalDoc(){
    $checkData = DB::table('user_object_auth')
    ->where('userid', Auth::user()->id)
    ->where('object_name', 'ALLOW_UPLOAD_ORIGINAL_DOC')
    ->first();
    if($checkData){
        if($checkData->object_val === "Y"){
            return 1;
        }else{
            return 0;
        }
    }else{
        return 0;
    }
}

function allowDownloadOrginalDoc(){
    $checkData = DB::table('user_object_auth')
    ->where('userid', Auth::user()->id)
    ->where('object_name', 'ALLOW_DOWNLOAD_ORIGINAL_DOC')
    ->first();
    if($checkData){
        if($checkData->object_val === "Y"){
            return 1;
        }else{
            return 0;
        }
    }else{
        return 0;
    }
}

function getJabatanCode(){
    $checkData = DB::table('t_jabatan')
    ->where('id', Auth::user()->jabatanid)
    ->first();
    if($checkData){
        return $checkData->jbtncode;
    }else{
        return NULL;
    }
}

function getUserEmail($userid){
    $checkData = DB::table('users')
    ->where('id', $userid)
    ->first();
    if($checkData){
        return $checkData->email;
    }else{
        return NULL;
    }
}

function checkAllowedAuth($objectName){
    $checkData = DB::table('user_object_auth')
    ->where('userid', Auth::user()->id)
    ->where('object_name', $objectName)
    ->first();
    if($checkData){
        if($checkData->object_val === "Y"){
            return 1;
        }else{
            return 0;
        }
    }else{
        return 0;
    }
}

function apiIpdApp(){
    $ipdapi    = DB::table('general_setting')->where('setting_name', 'IPD_MODEL_API')->first();
    return $ipdapi->setting_value;
}

function getAppTheme(){
    $ipdapi    = DB::table('general_setting')->where('setting_name', 'APP_THEME')->first();
    return $ipdapi->setting_value;
}

function getAppBgImage(){
    $ipdapi    = DB::table('general_setting')->where('setting_name', 'APP_BGIMAGE')->first();
    return $ipdapi->setting_value;
}

function getUserDepartment(){
    $userDept = DB::table('t_department')->where('deptid', Auth::user()->deptid)->first();
    return $userDept->department;
}

function getDepartmentByID($id){
    $userDept = DB::table('t_department')->where('deptid', $id)->first();
    if($userDept){
        return $userDept->department;
    }else{
        return '';
    }
}

function getUserNameByID($id){
    $userDept = DB::table('users')->where('id', $id)->orWhere('email', $id)->first();
    return $userDept->name;
}

function generateBudgetDcnNumber($tahun, $bulan, $tgl, $dept, $deptname){
    $dcnNumber = 'PTA-'.$deptname.'/'.$tahun.$bulan.$tgl;

    $getdata = DB::table('t_nriv_budget')
               ->where('tahun',  $tahun)
               ->where('object', 'BUDGET')
               ->where('bulan',  $bulan)
               ->where('tanggal',  $tgl)
               ->where('deptid', $dept)
               ->first();
    
    if($getdata){
        DB::beginTransaction();
        try{
            $leadingZero = '';
            if(strlen($getdata->lastnumber) == 5){
                $leadingZero = '0';
            }elseif(strlen($getdata->lastnumber) == 4){
                $leadingZero = '00';
            }elseif(strlen($getdata->lastnumber) == 3){
                $leadingZero = '000';
            }elseif(strlen($getdata->lastnumber) == 2){
                $leadingZero = '0000';
            }elseif(strlen($getdata->lastnumber) == 1){
                $leadingZero = '00000';
            }

            $lastnum = ($getdata->lastnumber*1) + 1;

            if($leadingZero == ''){
                $dcnNumber = $dcnNumber. $lastnum; 
            }else{
                $dcnNumber = $dcnNumber . $leadingZero . $lastnum; 
            }

            // dd($leadingZero);

            DB::table('t_nriv_budget')
            ->where('tahun',  $tahun)
            ->where('object', 'BUDGET')
            ->where('bulan',  $bulan)
            ->where('tanggal',  $tgl)
            ->where('deptid', $dept)
            ->update([
                'lastnumber' => $lastnum
            ]);

            DB::commit();
            return $dcnNumber;
        }catch(\Exception $e){
            DB::rollBack();
            return null;
        }
    }else{
        $dcnNumber = $dcnNumber.'000001';
        DB::beginTransaction();
        try{
            DB::table('t_nriv_budget')->insert([
                'object'          => 'BUDGET',
                'tahun'           => $tahun,
                'bulan'           => $bulan,
                'tanggal'         => $tgl,
                'deptid'          => $dept,
                'lastnumber'      => '1',
                'createdon'       => date('Y-m-d H:m:s'),
                'createdby'       => Auth::user()->email ?? Auth::user()->username
            ]);
            DB::commit();
            return $dcnNumber;
        }catch(\Exception $e){
            DB::rollBack();
            return null;
        }
    }    
}

function generatePbjNumber($tahun, $bulan, $tgl){
    $dcnNumber = 'PBJ/'.$tahun.$bulan.$tgl;
    // dd($dcnNumber);
    $getdata = DB::table('t_nriv_budget')
               ->where('tahun',  $tahun)
               ->where('object', 'PBJ')
               ->where('bulan',  $bulan)
               ->where('tanggal',  $tgl)
            //    ->where('deptid', $dept)
               ->first();
    
    if($getdata){
        DB::beginTransaction();
        try{
            $leadingZero = '';
            if(strlen($getdata->lastnumber) == 5){
                $leadingZero = '0';
            }elseif(strlen($getdata->lastnumber) == 4){
                $leadingZero = '00';
            }elseif(strlen($getdata->lastnumber) == 3){
                $leadingZero = '000';
            }elseif(strlen($getdata->lastnumber) == 2){
                $leadingZero = '0000';
            }elseif(strlen($getdata->lastnumber) == 1){
                $leadingZero = '00000';
            }

            $lastnum = ($getdata->lastnumber*1) + 1;

            if($leadingZero == ''){
                $dcnNumber = $dcnNumber. $lastnum; 
            }else{
                $dcnNumber = $dcnNumber . $leadingZero . $lastnum; 
            }

            // dd($leadingZero);

            DB::table('t_nriv_budget')
            ->where('tahun',  $tahun)
            ->where('object', 'PBJ')
            ->where('bulan',  $bulan)
            ->where('tanggal',  $tgl)
            // ->where('deptid', $dept)
            ->update([
                'lastnumber' => $lastnum
            ]);

            DB::commit();
            return $dcnNumber;
        }catch(\Exception $e){
            DB::rollBack();
            return null;
        }
    }else{
        $dcnNumber = $dcnNumber.'000001';
        DB::beginTransaction();
        try{
            DB::table('t_nriv_budget')->insert([
                'object'          => 'PBJ',
                'tahun'           => $tahun,
                'bulan'           => $bulan,
                'tanggal'         => $tgl,
                // 'deptid'          => $dept,
                'lastnumber'      => '1',
                'createdon'       => date('Y-m-d H:m:s'),
                'createdby'       => Auth::user()->email ?? Auth::user()->username
            ]);
            DB::commit();
            return $dcnNumber;
        }catch(\Exception $e){
            DB::rollBack();
            return null;
        }
    }   
}

function generatePRNumber($tahun, $bulan, $tgl, $dept, $deptname){
    $dcnNumber = 'PR-'.$deptname.'/'.$tahun.$bulan.$tgl;
    // dd($dcnNumber);
    $getdata = DB::table('t_nriv_budget')
               ->where('tahun',  $tahun)
               ->where('object', 'PR')
               ->where('bulan',  $bulan)
               ->where('tanggal',  $tgl)
               ->where('deptid', $dept)
               ->first();
    
    if($getdata){
        DB::beginTransaction();
        try{
            $leadingZero = '';
            if(strlen($getdata->lastnumber) == 5){
                $leadingZero = '0';
            }elseif(strlen($getdata->lastnumber) == 4){
                $leadingZero = '00';
            }elseif(strlen($getdata->lastnumber) == 3){
                $leadingZero = '000';
            }elseif(strlen($getdata->lastnumber) == 2){
                $leadingZero = '0000';
            }elseif(strlen($getdata->lastnumber) == 1){
                $leadingZero = '00000';
            }

            $lastnum = ($getdata->lastnumber*1) + 1;

            if($leadingZero == ''){
                $dcnNumber = $dcnNumber. $lastnum; 
            }else{
                $dcnNumber = $dcnNumber . $leadingZero . $lastnum; 
            }

            // dd($leadingZero);

            DB::table('t_nriv_budget')
            ->where('tahun',  $tahun)
            ->where('object', 'PR')
            ->where('bulan',  $bulan)
            ->where('tanggal',  $tgl)
            ->where('deptid', $dept)
            ->update([
                'lastnumber' => $lastnum
            ]);

            DB::commit();
            return $dcnNumber;
        }catch(\Exception $e){
            DB::rollBack();
            return null;
        }
    }else{
        $dcnNumber = $dcnNumber.'000001';
        DB::beginTransaction();
        try{
            DB::table('t_nriv_budget')->insert([
                'object'          => 'PR',
                'tahun'           => $tahun,
                'bulan'           => $bulan,
                'tanggal'         => $tgl,
                'deptid'          => $dept,
                'lastnumber'      => '1',
                'createdon'       => date('Y-m-d H:m:s'),
                'createdby'       => Auth::user()->email ?? Auth::user()->username
            ]);
            DB::commit();
            return $dcnNumber;
        }catch(\Exception $e){
            DB::rollBack();
            return null;
        }
    }   
}

function generatePONumber($tahun, $bulan, $tgl){
    $dcnNumber = 'PO/'.$tahun.$bulan.$tgl;
    // dd($dcnNumber);
    $getdata = DB::table('t_nriv_budget')
               ->where('tahun',  $tahun)
               ->where('object', 'PO')
               ->where('bulan',  $bulan)
               ->where('tanggal',  $tgl)
            //    ->where('deptid', $dept)
               ->first();
    
    if($getdata){
        DB::beginTransaction();
        try{
            $leadingZero = '';
            if(strlen($getdata->lastnumber) == 5){
                $leadingZero = '0';
            }elseif(strlen($getdata->lastnumber) == 4){
                $leadingZero = '00';
            }elseif(strlen($getdata->lastnumber) == 3){
                $leadingZero = '000';
            }elseif(strlen($getdata->lastnumber) == 2){
                $leadingZero = '0000';
            }elseif(strlen($getdata->lastnumber) == 1){
                $leadingZero = '00000';
            }

            $lastnum = ($getdata->lastnumber*1) + 1;

            if($leadingZero == ''){
                $dcnNumber = $dcnNumber. $lastnum; 
            }else{
                $dcnNumber = $dcnNumber . $leadingZero . $lastnum; 
            }

            // dd($leadingZero);

            DB::table('t_nriv_budget')
            ->where('tahun',  $tahun)
            ->where('object', 'PO')
            ->where('bulan',  $bulan)
            ->where('tanggal',  $tgl)
            // ->where('deptid', $dept)
            ->update([
                'lastnumber' => $lastnum
            ]);

            DB::commit();
            return $dcnNumber;
        }catch(\Exception $e){
            DB::rollBack();
            return null;
        }
    }else{
        $dcnNumber = $dcnNumber.'000001';
        DB::beginTransaction();
        try{
            DB::table('t_nriv_budget')->insert([
                'object'          => 'PO',
                'tahun'           => $tahun,
                'bulan'           => $bulan,
                'tanggal'         => $tgl,
                // 'deptid'          => $dept,
                'lastnumber'      => '1',
                'createdon'       => date('Y-m-d H:m:s'),
                'createdby'       => Auth::user()->email ?? Auth::user()->username
            ]);
            DB::commit();
            return $dcnNumber;
        }catch(\Exception $e){
            DB::rollBack();
            return null;
        }
    }   
}

function generateGRPONumber($tahun, $bulan){
    $dcnNumber = 'RPO/'.$tahun.$bulan;
    // dd($dcnNumber);
    $getdata = DB::table('t_nriv_budget')
               ->where('tahun',  $tahun)
               ->where('object', 'GRPO')
               ->where('bulan',  $bulan)
            //    ->where('tanggal',  $tgl)
            //    ->where('deptid', $dept)
               ->first();
    
    if($getdata){
        DB::beginTransaction();
        try{
            $leadingZero = '';
            if(strlen($getdata->lastnumber) == 5){
                $leadingZero = '0';
            }elseif(strlen($getdata->lastnumber) == 4){
                $leadingZero = '00';
            }elseif(strlen($getdata->lastnumber) == 3){
                $leadingZero = '000';
            }elseif(strlen($getdata->lastnumber) == 2){
                $leadingZero = '0000';
            }elseif(strlen($getdata->lastnumber) == 1){
                $leadingZero = '00000';
            }

            $lastnum = ($getdata->lastnumber*1) + 1;

            if($leadingZero == ''){
                $dcnNumber = $dcnNumber. $lastnum; 
            }else{
                $dcnNumber = $dcnNumber . $leadingZero . $lastnum; 
            }

            // dd($leadingZero);

            DB::table('t_nriv_budget')
            ->where('tahun',  $tahun)
            ->where('object', 'GRPO')
            ->where('bulan',  $bulan)
            // ->where('tanggal',  $tgl)
            // ->where('deptid', $dept)
            ->update([
                'lastnumber' => $lastnum
            ]);

            DB::commit();
            // dd($dcnNumber);
            return $dcnNumber;
        }catch(\Exception $e){
            DB::rollBack();
            return null;
        }
    }else{
        $dcnNumber = $dcnNumber.'000001';
        DB::beginTransaction();
        try{
            DB::table('t_nriv_budget')->insert([
                'object'          => 'GRPO',
                'tahun'           => $tahun,
                'bulan'           => $bulan,
                'tanggal'         => '01',
                // 'deptid'          => $dept,
                'lastnumber'      => '1',
                'createdon'       => date('Y-m-d H:m:s'),
                'createdby'       => Auth::user()->email ?? Auth::user()->username
            ]);
            DB::commit();
            return $dcnNumber;
        }catch(\Exception $e){
            DB::rollBack();
            // dd($e->getMessage());
            return null;
        }
    }   
}

function generateWONumber($tahun, $bulan){
    $dcnNumber = 'WO/'.$tahun.$bulan;
    // dd($dcnNumber);
    $getdata = DB::table('t_nriv_budget')
               ->where('tahun',  $tahun)
               ->where('object', 'WO')
               ->where('bulan',  $bulan)
            //    ->where('tanggal',  $tgl)
            //    ->where('deptid', $dept)
               ->first();
    
    if($getdata){
        DB::beginTransaction();
        try{
            $leadingZero = '';
            if(strlen($getdata->lastnumber) == 5){
                $leadingZero = '0';
            }elseif(strlen($getdata->lastnumber) == 4){
                $leadingZero = '00';
            }elseif(strlen($getdata->lastnumber) == 3){
                $leadingZero = '000';
            }elseif(strlen($getdata->lastnumber) == 2){
                $leadingZero = '0000';
            }elseif(strlen($getdata->lastnumber) == 1){
                $leadingZero = '00000';
            }

            $lastnum = ($getdata->lastnumber*1) + 1;

            if($leadingZero == ''){
                $dcnNumber = $dcnNumber. $lastnum; 
            }else{
                $dcnNumber = $dcnNumber . $leadingZero . $lastnum; 
            }

            // dd($leadingZero);

            DB::table('t_nriv_budget')
            ->where('tahun',  $tahun)
            ->where('object', 'WO')
            ->where('bulan',  $bulan)
            // ->where('tanggal',  $tgl)
            // ->where('deptid', $dept)
            ->update([
                'lastnumber' => $lastnum
            ]);

            DB::commit();
            // dd($dcnNumber);
            return $dcnNumber;
        }catch(\Exception $e){
            DB::rollBack();
            return null;
        }
    }else{
        $dcnNumber = $dcnNumber.'000001';
        DB::beginTransaction();
        try{
            DB::table('t_nriv_budget')->insert([
                'object'          => 'WO',
                'tahun'           => $tahun,
                'bulan'           => $bulan,
                'tanggal'         => '01',
                // 'deptid'          => $dept,
                'lastnumber'      => '1',
                'createdon'       => date('Y-m-d H:m:s'),
                'createdby'       => Auth::user()->email ?? Auth::user()->username
            ]);
            DB::commit();
            return $dcnNumber;
        }catch(\Exception $e){
            DB::rollBack();
            // dd($e->getMessage());
            return null;
        }
    } 
}