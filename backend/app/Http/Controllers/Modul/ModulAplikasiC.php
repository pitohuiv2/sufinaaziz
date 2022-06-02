<?php


namespace App\Http\Controllers\Modul;

use App\Http\Controllers\ApiController;
use App\Master\KelompokUser;
// use App\Master\MapLoginUserToModulAplikasi;
use App\Master\MapObjekModulAplikasiToModulAplikasi;
// use App\Master\WaktuLogin;
use App\Master\ModulAplikasi;
use App\Master\ObjekModulAplikasi;
use App\Web\LoginUser;
use Illuminate\Http\Request;
use App\Traits\Valet;
use DB;
use Webpatser\Uuid\Uuid;

class ModulAplikasiC extends ApiController
{

    use Valet;

    public function __construct()
    {
        parent::__construct($skip_authentication = false);
    }

    public function getMenu(Request $request)
    {
        $dataraw3 = [];
        $dataRaw = \DB::table('objekmodulaplikasi_s as oma')
            ->join('mapobjekmodulaplikasitomodulaplikasi_s as acdc', 'acdc.objekmodulaplikasiid', '=', 'oma.id')
            ->join('maploginusertomodulaplikasi_s as maps',
                function ($join) {
                    $join->on('maps.objectmodulaplikasifk', '=', 'acdc.modulaplikasiid');
                })
//				'maps.objectmodulaplikasifk', '=', 'acdc.modulaplikasiid')
            ->join('modulaplikasi_s as ma', 'ma.id', '=', 'acdc.modulaplikasiid')
//            ->where('oma.kdprofile', $kdProfile)
            ->where('oma.statusenabled', true)
            ->where('ma.reportdisplay', 'Menu')
            ->where('maps.objectloginuserfk', $request['idUser'])
            ->select('oma.id', 'oma.kdobjekmodulaplikasihead', 'oma.objekmodulaplikasi', 'oma.alamaturlform', 'ma.modulaplikasi',
                'acdc.modulaplikasiid', 'oma.kodeexternal')
            ->groupBy('oma.id', 'oma.kdobjekmodulaplikasihead', 'oma.objekmodulaplikasi', 'oma.alamaturlform', 'ma.modulaplikasi',
                'acdc.modulaplikasiid', 'oma.kodeexternal', 'oma.nourut')
            ->orderBy('oma.nourut');
        $dataRaw = $dataRaw->get();
        foreach ($dataRaw as $dataRaw2) {
//                if ((integer)$dataRaw2->id < 100) {
            if ($dataRaw2->kdobjekmodulaplikasihead == null) {
                if ($dataRaw2->alamaturlform != null || $dataRaw2->alamaturlform != '') {
                    $dataraw3[] = array(
                        'id' => $dataRaw2->id,
                        'parent_id' => 0,
                        'icon' => 'pi pi-fw pi-chevron-circle-right',
                        'label' => $dataRaw2->objekmodulaplikasi,
                        'routerLink' => [str_replace('#', '', $dataRaw2->alamaturlform)]
                    );
                } else {
                    $dataraw3[] = array(
                        'id' => $dataRaw2->id,
                        'parent_id' => 0,
                        'icon' => 'pi pi-fw pi-align-center',
                        'label' => $dataRaw2->objekmodulaplikasi,

                    );
                }

            } else {
                if ($dataRaw2->kdobjekmodulaplikasihead != null) {
                    if ($dataRaw2->alamaturlform != null || $dataRaw2->alamaturlform != '') {
                        $dataraw3[] = array(
                            'id' => $dataRaw2->id,
                            'parent_id' => $dataRaw2->kdobjekmodulaplikasihead,
                            'label' => $dataRaw2->objekmodulaplikasi,
                            'icon' => 'pi pi-fw pi-chevron-circle-right',
                            'routerLink' => [str_replace('#', '', $dataRaw2->alamaturlform)]
                        );
                    } else {
                        $dataraw3[] = array(
                            'id' => $dataRaw2->id,
                            'parent_id' => $dataRaw2->kdobjekmodulaplikasihead,
                            'label' => $dataRaw2->objekmodulaplikasi,
                            'icon' => 'pi pi-fw pi-align-center',
                        );
                    }
                } else {
                    if ($dataRaw2->modulaplikasiid == $request['id']) {
                        if ($dataRaw2->alamaturlform != null || $dataRaw2->alamaturlform != '') {
                            $dataraw3[] = array(
                                'id' => $dataRaw2->id,
                                'parent_id' => $dataRaw2->kdobjekmodulaplikasihead,
                                'label' => $dataRaw2->objekmodulaplikasi,
                                'icon' => 'pi pi-fw pi-chevron-circle-right',
                                'routerLink' => [str_replace('#', '', $dataRaw2->alamaturlform)]
                            );
                        } else {
                            $dataraw3[] = array(
                                'id' => $dataRaw2->id,
                                'parent_id' => $dataRaw2->kdobjekmodulaplikasihead,
                                'label' => $dataRaw2->objekmodulaplikasi,
                                'icon' => 'pi pi-fw pi-align-center',
                            );
                        }
                    }
                }
            }
        }
        $data = $dataraw3;
        function recursiveElements($data)
        {
            $elements = [];
            $tree = [];
            foreach ($data as &$element) {
                $id = $element['id'];
                $parent_id = $element['parent_id'];

                $elements[$id] = &$element;
                if (isset($elements[$parent_id])) {
                    $elements[$parent_id]['items'][] = &$element;
                } else {
                    if ($parent_id <= 10) {
                        $tree[] = &$element;
                    }
                }
            }
            return $tree;
        }

        $data = recursiveElements($data);

        return $this->respond($data);
    }

    public function getModulAplikasi(Request $request)
    {
        $kdProfile = $this->getDataKdProfile($request);
        if ($request['jenis'] == 'objekMenuRecursive') {
            $dataRaw = \DB::table('objekmodulaplikasi_s as oma')
                ->join('mapobjekmodulaplikasitomodulaplikasi_s as acdc', 'acdc.objekmodulaplikasiid', '=', 'oma.id')
                ->join('modulaplikasi_s as ma', 'ma.id', '=', 'acdc.modulaplikasiid')
                ->where('oma.statusenabled', true)
                ->where('acdc.modulaplikasiid', $request['id'])
//                ->where('oma.kdprofile', $idProfile)
                ->select('oma.*', 'acdc.modulaplikasiid')
                ->orderBy('oma.nourut');
            $dataRaw = $dataRaw->get();
            $dataraw3 = [];
            foreach ($dataRaw as $dataRaw2) {
//                if ((integer)$dataRaw2->id < 100) {
                if ($dataRaw2->kdobjekmodulaplikasihead == null) {
                    $dataraw3[] = array(
                        'id' => $dataRaw2->id,
                        'parent_id' => 0,
                        'modulaplikasiid' => $dataRaw2->modulaplikasiid,
                        'subCategoryName' => $dataRaw2->id . '_' . $dataRaw2->objekmodulaplikasi,
                        'nourut' => $dataRaw2->nourut,
                        'keterangan' => $dataRaw2->keterangan,
                        'alamaturlform' => $dataRaw2->alamaturlform,
                        'fungsi' => $dataRaw2->fungsi,

                        'key' => $dataRaw2->id,
                        'label' => $dataRaw2->objekmodulaplikasi,
                        'data' => $dataRaw2->objekmodulaplikasi,
                        'expandedIcon' => "pi pi-folder-open",
                        'collapsedIcon' => "pi pi-folder",
                    );
                } else {
//                    if ((integer)$dataRaw2->id > 100) {
                    if ($dataRaw2->kdobjekmodulaplikasihead != null) {
                        $dataraw3[] = array(
                            'id' => $dataRaw2->id,
                            'parent_id' => $dataRaw2->kdobjekmodulaplikasihead,
                            'subCategoryName' => $dataRaw2->id . '_' . $dataRaw2->objekmodulaplikasi,
                            'nourut' => $dataRaw2->nourut,
                            'keterangan' => $dataRaw2->keterangan,
                            'alamaturlform' => $dataRaw2->alamaturlform,
                            'fungsi' => $dataRaw2->fungsi,
                            'key' => $dataRaw2->id,
                            'label' => $dataRaw2->objekmodulaplikasi,
                            'data' => $dataRaw2->objekmodulaplikasi,
                            'expandedIcon' => "pi pi-folder-open",
                            'collapsedIcon' => "pi pi-folder",
                        );
                    } else {
                        if ($dataRaw2->modulaplikasiid == $request['id']) {
                            $dataraw3[] = array(
                                'id' => $dataRaw2->id,
                                'parent_id' => $dataRaw2->kdobjekmodulaplikasihead,
                                'subCategoryName' => $dataRaw2->id . '_' . $dataRaw2->objekmodulaplikasi,
                                'nourut' => $dataRaw2->nourut,
                                'keterangan' => $dataRaw2->keterangan,
                                'alamaturlform' => $dataRaw2->alamaturlform,
                                'fungsi' => $dataRaw2->fungsi,
                                'key' => $dataRaw2->id,
                                'label' => $dataRaw2->objekmodulaplikasi,
                                'data' => $dataRaw2->objekmodulaplikasi,
                                'expandedIcon' => "pi pi-folder-open",
                                'collapsedIcon' => "pi pi-folder",
                            );
                        }
                    }
                }
            }
            $data = $dataraw3;

            function recursiveElements($data)
            {
                $elements = [];
                $tree = [];
                foreach ($data as &$element) {
//                    $element['subCategories'] = [];
                    $id = $element['id'];
                    $parent_id = $element['parent_id'];

                    $elements[$id] = &$element;
                    if (isset($elements[$parent_id])) {
                        $elements[$parent_id]['children'][] = &$element;
                    } else {
                        if ($parent_id <= 10) {
                            $tree[] = &$element;
                        }
                    }
                    //}
                }
                return $tree;
            }

            $data = recursiveElements($data);
        }
        if ($request['jenis'] == 'subsistemRecursive') {
//            $data = [
//                ['id' => 1, 'parent_id' => 0, 'title' => 'food'],
//                ['id' => 2, 'parent_id' => 1, 'title' => 'drinks'],
//                ['id' => 3, 'parent_id' => 2, 'title' => 'juice'],
//                ['id' => 4, 'parent_id' => 0, 'title' => 'furniture'],
//                ['id' => 5, 'parent_id' => 4, 'title' => 'tables']
//            ];

            $dataRaw = $data = \DB::table('modulaplikasi_s as ma')
                ->where('modulaplikasi', '<>', 'Belum Pilih Modul')
//                ->where('ma.kdprofile', (int)$kdProfile)
                ->orderBy('id')
                ->get();

            foreach ($dataRaw as $dataRaw2) {
//                if (isset($dataRaw2->kdmodulaplikasihead)){
                $dataraw3[] = array(
                    'id' => $dataRaw2->id,
                    'parent_id' => $dataRaw2->kdmodulaplikasihead,
                    'title' => $dataRaw2->modulaplikasi,
                );
//                }else{
//                    $dataraw3[] = array(
//                        'id' => $dataRaw2->id,
//                        'parent_id' => 0,
//                        'title' => $dataRaw2->modulaplikasi,
//                    );
//                }
            }

            $data = $dataraw3;

            function recursiveElements($data)
            {
                $elements = [];
                $tree = [];
                foreach ($data as &$element) {
                    $element['children'] = [];
                    $id = $element['id'];
                    $parent_id = $element['parent_id'];
                    $elements[$id] = &$element;
                    if (isset($elements[$parent_id])) {
                        $elements[$parent_id]['children'][] = &$element;
                    } else {
                        $tree[] = &$element;
                    }
                }
                return $tree;
            }

            $data = recursiveElements($data);
        }
        if ($request['jenis'] == 'subsistem') {
            $data = \DB::table('modulaplikasi_s as ma')
                ->whereNull('ma.kdmodulaplikasihead')
                ->where('reportdisplay', '=', 'Modul')
                ->where('ma.statusenabled', true)
//                ->where('ma.kdprofile', (int)$kdProfile)
                ->orderBy('id', 'desc');
            if (isset($request['id'])) {
                $data = $data->where('ma.id', $request['id']);
            };
            $data = $data->get();
        }
        if ($request['jenis'] == 'modulaplikasi') {
            $data = \DB::table('modulaplikasi_s as ma')
                ->where('ma.statusenabled', true)
//                ->where('ma.kdprofile', (int)$kdProfile)
                ->orderBy('ma.id')
                ->whereNotNull('ma.kdmodulaplikasihead');
            if (isset($request['id'])) {
                $data = $data->where('ma.kdmodulaplikasihead', $request['id']);
            }
            if (isset($request['idhead'])) {
                $data = $data->where('ma.id', $request['idhead']);
            };
            $data = $data->get();
        }
        if ($request['jenis'] == 'objectmodulaplikasi') {
            $dataHead = \DB::table('objekmodulaplikasi_s as oma')
                ->whereNull('kdobjekmodulaplikasihead')
                ->where('statusenabled', true)
//                ->where('oma.kdprofile', (int)$kdProfile)
                ->orderBy('id')
                ->get();

            $dataAnak = \DB::table('objekmodulaplikasi_s as oma')
                ->join('mapobjekmodulaplikasitomodulaplikasi_s as acdc', 'acdc.objekmodulaplikasiid', '=', 'oma.id')
                ->whereNotNull('oma.kdobjekmodulaplikasihead')
                ->where('oma.statusenabled', true)
//                ->where('oma.kdprofile', (int)$kdProfile)
                ->select('oma.*');
            if (isset($request['id'])) {
                $dataAnak = $dataAnak->where('acdc.modulaplikasiid', $request['id']);
            }
            $dataAnak = $dataAnak->get();
            foreach ($dataHead as $hulu) {
                //$result[]='';
                $result = null;
                foreach ($dataAnak as $buntut) {
                    if ($hulu->id == $buntut->kdobjekmodulaplikasihead) {
                        $result[] = array(
                            'id' => $buntut->id,
                            'modul' => $buntut->objekmodulaplikasi,
                        );
                    }
                }
                $resultHead[] = array(
                    'id' => $hulu->id,
                    'modul' => $hulu->objekmodulaplikasi,
                    'anak' => $result,
                );
            }
            $data = $resultHead;
        }
        if ($request['jenis'] == 'objekmodultokelompokuser') {

            $data5 = \DB::table('mapobjekmodultokelompokuser_s as momku')
                ->leftjoin('mapobjekmodulaplikasitomodulaplikasi_s as acdc', 'momku.objectobjekmodulaplikasifk', '=', 'acdc.objekmodulaplikasiid')
                ->join('kelompokuser_s as ku', 'momku.objectkelompokuserfk', '=', 'ku.id')
                ->select('momku.simpan', 'momku.edit', 'momku.hapus', 'momku.cetak', 'ku.id as kuid', 'ku.kelompokuser', 'momku.id as momkuid', 'momku.objectobjekmodulaplikasifk as omaid', 'acdc.modulaplikasiid')
                ->where('acdc.statusenabled', true)
                ->where('momku.kdprofile', (int)$kdProfile);
//                ->where('oma.statusenabled',true);
//            if (isset($request['id'])) {
//                $data5 = $data5->where('acdc.modulaplikasiid', $request['id']);
//            }
            if (isset($request['omaid'])) {
                $data5 = $data5->where('momku.objectobjekmodulaplikasifk', $request['omaid']);
            }
            $data5 = $data5->get();
            $data2 = ObjekModulAplikasi::where('id', $request['omaid'])->first();
            if (isset($data2->kdobjekmodulaplikasihead)) {
                $data3 = ObjekModulAplikasi::where('id', $data2->kdobjekmodulaplikasihead)
//                                            ->where('kdprofile', (int)$kdProfile)
                    ->first();
                $objekmodulaplikasiSTR = $data3->objekmodulaplikasi;
                $kdobjekmodulaplikasihead = $data2->kdobjekmodulaplikasihead;
            } else {
                $objekmodulaplikasiSTR = '';
                $kdobjekmodulaplikasihead = '';
            }
            $data = array(
                'data1' => $data5,
                'data2' => array(
                    'kdobjekmodulaplikasihead' => $kdobjekmodulaplikasihead,
                    'objekmodulaplikasihead' => $objekmodulaplikasiSTR,
                    'objekmodulaplikasi' => $data2->objekmodulaplikasi,
                    'omaid' => $data2->id,
                    'fungsi' => $data2->fungsi,
                    'keterangan' => $data2->keterangan,
                    'nourut' => $data2->nourut,
                    'alamaturlform' => $data2->alamaturlform,
                )
            );
            ///$data=$data3;
        }
        if ($request['jenis'] == 'kelompokuser') {
            $data = KelompokUser::where('statusenabled', true)
                ->where('kdprofile', (int)$kdProfile)
                ->get();
        }
        return $this->respond($data);
    }

    public function objekModulAplikasi(Request $request)
    {
        $kdProfile = (int)$this->getDataKdProfile($request);
        DB::beginTransaction();
        try {
            if ($request['id'] == 0) {
                $objekmodulaplikasi = new ObjekModulAplikasi();
                $newID = ObjekModulAplikasi::max('id');
                $newID = $newID + 1;
                $objekmodulaplikasi->id = $newID;
                $objekmodulaplikasi->kdprofile = $kdProfile;
                $objekmodulaplikasi->statusenabled = true;
                $objekmodulaplikasi->norec = substr(Uuid::generate(), 0, 32);
                if (is_null($request['kdobjekmodulaplikasihead'])) {
                    $objekmodulaplikasi->kodeexternal = 'H';
                } else {
                    $objekmodulaplikasi->kodeexternal = null;
                }
                $objekmodulaplikasi->kdobjekmodulaplikasi = $newID;
                //add Map
                $newData = new MapObjekModulAplikasiToModulAplikasi();
                $newId2 = MapObjekModulAplikasiToModulAplikasi::max('id');
                $newId2 = $newId2 + 1;
                $newData->id = $newId2;
                $newData->kdprofile = $kdProfile;//0;
                $newData->statusenabled = true;
                $newData->norec = substr(Uuid::generate(), 0, 32);
                $newData->modulaplikasiid = intval($request['modulaplikasiid']);
                $newData->objekmodulaplikasiid = $newID;
                $newData->save();

            } else {
                $objekmodulaplikasi = ObjekModulAplikasi::where('id', $request['id'])->where('kdprofile', $kdProfile)->first();
            }
            $objekmodulaplikasi->fungsi = $request['fungsi'];
            $objekmodulaplikasi->keterangan = $request['keterangan'];
            $objekmodulaplikasi->objekmodulaplikasi = $request['objekmodulaplikasi'];
            $objekmodulaplikasi->nourut = intval($request['nourut']);
            $objekmodulaplikasi->kdobjekmodulaplikasihead = $request['kdobjekmodulaplikasihead'];
            $objekmodulaplikasi->alamaturlform = $request['alamaturlform'];
            $objekmodulaplikasi->save();
            $this->transStatus = true;
        } catch (\Exception $e) {
            $this->transStatus = false;

        }

        if ($this->transStatus) {
            $transMessage = "Sukses";
            DB::commit();
            $result = array(
                "status" => 201,
                "message" => $transMessage,
                "as" => 'er',
            );

        } else {
            $transMessage = "Simpan Gagal";
            DB::rollBack();
            $result = array(
                "status" => 400,
                "message" => $transMessage,
                "as" => 'er',
            );
        }
        return $this->setStatusCode($result['status'])->respond($result, $transMessage);
    }

    public function HapusObjekModulAplikasi(Request $request)
    {
        $kdProfile = (int)$this->getDataKdProfile($request);
        DB::beginTransaction();
        try {

            $dataDelete = ObjekModulAplikasi::where('id', $request['id'])->update(
                ['statusenabled' => 'f']
            );

            $dataDelete = MapObjekModulAplikasiToModulAplikasi::where('objekmodulaplikasiid', $request['id'])->update(
                ['statusenabled' => 'f']
            );

            $this->transStatus = true;
        } catch (\Exception $e) {
            $this->transStatus = false;

        }
        if ($this->transStatus) {
            $transMessage = "Sukses";
            DB::commit();
            $result = array(
                "status" => 201,
                "message" => $transMessage,
                "as" => 'er',
            );

        } else {
            $transMessage = "Hapus Gagal";
            DB::rollBack();
            $result = array(
                "status" => 400,
                "message" => $transMessage,
                "as" => 'er',
            );
        }
        return $this->setStatusCode($result['status'])->respond($result, $transMessage);
    }
    public function saveModulApp(Request $request)
    {
        $kdProfile = (int)$this->getDataKdProfile($request);
        DB::beginTransaction();
        try {
            if ($request['id'] == '') {
                $objekmodulaplikasi = new ModulAplikasi();
                $id =ModulAplikasi::max('id') +1;
                $objekmodulaplikasi->id = $id;
                $objekmodulaplikasi->kdprofile = $kdProfile;
                $objekmodulaplikasi->statusenabled = true;
                $objekmodulaplikasi->norec = substr(Uuid::generate(), 0, 32);
                $objekmodulaplikasi->kdmodulaplikasi =$id;
            } else {
                $objekmodulaplikasi = ModulAplikasi::where('id', $request['id'])->where('kdprofile', $kdProfile)->first();
            }

            $objekmodulaplikasi->modulaplikasi = $request['modulaplikasi'];
            $objekmodulaplikasi->nourut = $request['nourut'];
            $objekmodulaplikasi->reportdisplay = $request['reportdisplay'];
            $objekmodulaplikasi->kdmodulaplikasihead = $request['kdmodulaplikasihead'];
            $objekmodulaplikasi->save();
            $this->transStatus = true;
        } catch (\Exception $e) {
            $this->transStatus = false;

        }

        if ($this->transStatus) {
            $transMessage = "Sukses";
            DB::commit();
            $result = array(
                "status" => 201,
                "message" => $transMessage,
                "as" => 'er',
            );

        } else {
            $transMessage = "Simpan Gagal";
            DB::rollBack();
            $result = array(
                "status" => 400,
                "message" => $transMessage,
                "e" => $e->getMessage(),
                "as" => 'er',
            );
        }
        return $this->setStatusCode($result['status'])->respond($result, $transMessage);
    }
    public function hapusModulApp(Request $request)
    {
        $kdProfile = (int)$this->getDataKdProfile($request);
        DB::beginTransaction();
        try {
                $objekmodulaplikasi = ModulAplikasi::where('id', $request['id'])->where('kdprofile', $kdProfile)->update([
                    'statusenabled'=>false
                ]);

            $this->transStatus = true;
        } catch (\Exception $e) {
            $this->transStatus = false;

        }

        if ($this->transStatus) {
            $transMessage = "Data Terhapus";
            DB::commit();
            $result = array(
                "status" => 201,
                "message" => $transMessage,
                "as" => 'er',
            );

        } else {
            $transMessage = "Simpan Gagal";
            DB::rollBack();
            $result = array(
                "status" => 400,
                "message" => $transMessage,
                "as" => 'er',
            );
        }
        return $this->setStatusCode($result['status'])->respond($result, $transMessage);
    }


}
