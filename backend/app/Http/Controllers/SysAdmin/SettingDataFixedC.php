<?php

/**
 * Created by PhpStorm.
 * User: Sany
 * Date: 11/28/2017
 * Time: 8:35 PM
 */

namespace App\Http\Controllers\SysAdmin;

use App\Http\Controllers\ApiController;
// use App\Master\MapKelompokPasientoPenjamin;
// use App\Master\Rekanan;
use App\Master\SettingDataFixed;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\Traits\CrudMaster;
use App\Traits\Valet;

// use App\Master\ChartOfAccount;
use DB;
use App\Traits\Dev\Designation;
use Date;

class SettingDataFixedC extends ApiController
{
    use CrudMaster;
    use Designation;
    use Valet;

    public function __construct()
    {
        parent::__construct($skip_authentication = false);
    }

    protected function getSettingDataFixedGeneric($namaField, Request $request)
    {
        $kdProfile = (int) $this->getDataKdProfile($request);
        $set = SettingDataFixed::where('namafield', $namaField)->where('koders', $kdProfile)->first();
        return $set->nilaifield;
    }
    public function getKelompokSettingDataFix(Request $request)
    {
        $kdProfile = (int) $this->getDataKdProfile($request);
        $data = DB::table('settingdatafixedmt')
            ->select(DB::raw("case when kelompok is null then 'Lain-lain' else kelompok end as kelompok"))
            ->where('koders', $kdProfile)
            ->groupBy('kelompok')
            ->orderBy('kelompok')
            ->get();
        $result = array(
            'data' => $data,
            'as' => 'Xoxo'
        );
        return $this->respond($result);
    }
    public function getSettingDetail(Request $request)
    {
        $kdProfile = (int) $this->getDataKdProfile($request);
        if ($request['kelompok'] == 'Lain-lain') {
            $request['kelompok']  = null;
        }
        $dataRaw = \DB::table('settingdatafixedmt')
            ->where('kelompok', $request['kelompok'])
            ->where('koders', $kdProfile)
            ->select('*', 'keteranganfungsi as caption', 'nilaifield')
            ->orderBy('id');
        $dataRaw = $dataRaw->get();
        $dataraw3A = [];

        foreach ($dataRaw as $dataRaw2) {
            $head = '';
            $type =  $dataRaw2->typefield;
            if (
                stripos($dataRaw2->typefield, 'Str') !== FALSE
                || stripos($dataRaw2->typefield, 'Int') !== FALSE
                || stripos($dataRaw2->typefield, 'Char') !== FALSE
            ) {
                if ($dataRaw2->tabelrelasi != null) {
                    $type = 'combobox';
                } else {
                    $type = 'textbox';
                }
            } elseif ($dataRaw2->typefield == 'combobox') {
                $type = 'combobox';
            } else {
                $type = 'textbox';
            }

            $dataraw3A[] = array(
                'koders' => $dataRaw2->koders,
                'aktif' => $dataRaw2->aktif,
                // 'kodeexternal'=> $dataRaw2->kodeexternal,
                // 'namaexternal' => $dataRaw2->namaexternal,
                // 'reportdisplay' => $dataRaw2->reportdisplay,
                'fieldkeytabelrelasi' => $dataRaw2->fieldkeytabelrelasi,
                'caption' => $head . $dataRaw2->caption,

                'cbotable' => $dataRaw2->tabelrelasi,
                'fieldreportdisplaytabelrelasi' => $dataRaw2->fieldreportdisplaytabelrelasi,
                'keteranganfungsi' => $dataRaw2->keteranganfungsi,
                'namafield' => $dataRaw2->namafield,
                'id' => $dataRaw2->id,
                'nilaifield' => $dataRaw2->nilaifield,
                'tabelrelasi' => $dataRaw2->tabelrelasi,
                'typefield' => $dataRaw2->typefield,
                'type' => $type,
                'kelompok' => $dataRaw2->kelompok,
                'value' => $dataRaw2->nilaifield,
                'text' => $dataRaw2->fieldreportdisplaytabelrelasi,
            );
        }

        $result = array(
            'kolom1' => $dataraw3A,
            'message' => 'Xoxo',
        );

        return $this->respond($result);
    }
    public function getComboPart(Request $request)
    {
        $kdProfile = (int) $this->getDataKdProfile($request);
        $id = $request['id'];
        $req = $request->all();
        $setting = DB::table('settingdatafixedmt')
            ->select('*')
            ->where('koders', $kdProfile)
            ->where('id', $id)
            ->get();
        $data = [];
        if (count($setting)) {
            $table =  $setting[0]->tabelrelasi;

            if ($table != null) {
                $namaField = strtolower($setting[0]->fieldkeytabelrelasi);
                $keyField = 'id'; //strtolower ($setting[0]->fieldkeytabelrelasi);
                $table = strtolower($table);
                $data  = \DB::table("$table")
                    ->select("$namaField as text", "$keyField as value")
                    ->where('aktif', true)
                    ->orderBy("$keyField");

                if (
                    isset($req['filter']['filters'][0]['value']) &&
                    $req['filter']['filters'][0]['value'] != "" &&
                    $req['filter']['filters'][0]['value'] != "undefined"
                ) {
                    $data = $data->where("$namaField", 'ilike', '%' . $req['filter']['filters'][0]['value'] . '%');
                };
                // $data = $data->take(10);
                $data = $data->get();
            }
        }


        return $this->respond($data);
    }
    public function updateSettingDataFix(Request $request)
    {
        DB::beginTransaction();
        $dataReq = $request->all();
        $kdProfile = (int) $this->getDataKdProfile($request);
        $data = $dataReq['data'];

        try {

            $i = 0;
            foreach ($data as $item) {
                if (is_array($item['values'])) {
                    $value = $item['values']['value'];
                    $text = $item['values']['text'];
                } else {
                    $value = $item['values'];
                    $text = '';
                }

                $EMRD =  SettingDataFixed::where('id', $item['id'])->where('koders', $kdProfile)->first();
                $EMRD->aktif = $dataReq['head'];
                $EMRD->nilaifield = $value;
                $EMRD->fieldreportdisplaytabelrelasi = $text;
                $EMRD->save();
                $i = $i + 1;
            }
            $transStatus = 'true';
        } catch (\Exception $e) {
            $transStatus = 'false';
        }
        $ReportTrans = 'Update Setting ';

        if ($transStatus == 'true') {
            $ReportTrans = $ReportTrans . "Sukses";
            DB::commit();
            $result = array(
                "status" => 201,
                "as" => 'Xoxo',
            );
        } else {
            $ReportTrans = $ReportTrans . " Gagal!!";
            DB::rollBack();
            $result = array(
                "status" => 400,
                "e" => $e->getMessage() . ' ' . $e->getLine(),
                "as" => 'Xoxo',
            );
        }

        return $this->setStatusCode($result['status'])->respond($result, $ReportTrans);
    }
    public function getFieldTable(Request $request)
    {;
        $table = $request['tablename'];
        $data  = DB::select(DB::raw("SELECT
            COLUMN_NAME
            FROM
            information_schema.COLUMNS
            WHERE
            TABLE_NAME = '$table';"));
        $result = array(
            "data" => $data,
            "as" => 'Xoxo',
        );
        return $this->respond($result);
    }
    public function getDataFromTable(Request $request)
    {;
        $table = $request['table_name'];
        $column = $request['column_name'];
        $data  = DB::select(DB::raw("
            select $column as name from $table
             "));
        $result = array(
            "data" => $data,
            "as" => 'Xoxo',
        );
        return $this->respond($result);
    }
    public function getReportDisplayTable(Request $request)
    {;
        $table = $request['table_name'];
        $column = $request['column_name'];
        $nilai = $request['nilai'];
        $data  = DB::select(DB::raw("
            select $column as id from $table
             "));
        $result = array(
            "data" => $data,
            "as" => 'Xoxo',
        );
        return $this->respond($result);
    }
    public function getTable(Request $request)
    {
        $kdProfile = (int) $this->getDataKdProfile($request);
        $req = $request->all();
        $data  = \DB::table("information_schema.tables")
            ->select("table_name")
            ->where('table_schema', '=', 'public')
            ->where('table_type', '=', 'BASE TABLE')
            ->orderBy("table_name");

        if (
            isset($req['name']) &&
            $req['name'] != "" &&
            $req['name'] != "undefined"
        ) {
            $data = $data->where("table_name", 'ilike', '%' .    $req['name'] . '%');
        };
        $data = $data->take(10);
        $data = $data->get();

        return $this->respond($data);
    }
    public function SaveSettingDataFixed(Request $request)
    {
        $kdProfile = (int) $this->getDataKdProfile($request);
        DB::beginTransaction();

        $idDataFixed = SettingDataFixed::max('id');
        $idDataFixed = $idDataFixed + 1;

        if ($request['datafixed']['iddatafixed'] == '') {
            $newDF = new SettingDataFixed();
            $newDF->id = $idDataFixed;

            $newDF->koders = $kdProfile;
            $newDF->aktif = true;
        } else {
            $newDF =  SettingDataFixed::where('id', $request['datafixed']['iddatafixed'])->where('koders', $kdProfile)->first();
        }

        $newDF->fieldkeytabelrelasi = $request['datafixed']['fieldkeytabelrelasi'];
        $newDF->fieldreportdisplaytabelrelasi = $request['datafixed']['fieldreportdisplaytabelrelasi'];
        $newDF->keteranganfungsi = $request['datafixed']['keteranganfungsi'];
        $newDF->namafield = $request['datafixed']['namafield'];
        $newDF->nilaifield = $request['datafixed']['nilai'];
        $newDF->tabelrelasi = $request['datafixed']['tabelrelasi'];
        $newDF->typefield = $request['datafixed']['typefield'];
        if (isset($request['datafixed']['kelompok'])) {
            $newDF->kelompok = $request['datafixed']['kelompok'];
        }
        try {
            $newDF->save();
            $transStatus = 'true';
        } catch (\Exception $e) {
            $transStatus = 'false';
        }

        if ($transStatus == 'true') {
            $ReportTrans = "Simpan Data Fixed Berhasil";
            DB::commit();
            $result = array(
                "status" => 201,
                "message" => $ReportTrans,
                "result" => $newDF,
                "as" => 'Xoxo',
            );
        } else {
            $ReportTrans = "Simpan Gagal";
            DB::rollBack();
            $result = array(
                "status" => 400,
                "message"  => $ReportTrans,
                "e"  => $e->getMessage() . ' ' . $e->getLine(),
                "as" => 'Xoxo',
            );
        }
        return $this->setStatusCode($result['status'])->respond($result, $ReportTrans);
    }
}
