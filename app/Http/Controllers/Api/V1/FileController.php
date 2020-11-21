<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\File;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use App\Http\Resources\FileCollection;

class FileController extends Controller
{
    private $file;

    public function __construct(File $file){
        $this->file = $file;


    }
    public function list(Request $request){
        $path = $request->has('path') ? $request->path : '/';
        $path = trim($path) != "" ? trim($path) : '/';
        $files = $this->file->where('path', $path)
            ->orderBy('created_at', 'DESC')
            ->paginate(20);

        $response = responseGenerator()->success(
            array(
                'files' => FileCollection::collection($files),
                'data'     => array(
                    'current_page' => $files->currentPage(),
                    'last_page'    => $files->lastPage(),
                    'total'    => $files->total()
                )

            ));

        return response()->json($response['data'], $response['status']);
    }

    public function upload(Request $request){
        $validator = Validator::make($request->all(), [
            'file'       => 'required|file',
            'title'      => 'nullable',
            'alt'        => 'nullable',
            'path'       => 'required',
        ]);

        if ($validator->fails()) {
            $errors = $validator->errors();
            $response = responseGenerator()->unprocessableEntity($errors->toArray(), true);
            return response()->json($response['data'], $response['status']);
        }
        $imageExtensions = ["art", "bmp", "blp", "cd5", "ase", "cit", "cpt", "cr2", "cut", "dds", "dib", "djvu", "egt", "exif", "gif", "gpl", "grf", "icns", "ico", "iff", "jng", "jpeg", "jpg", "jfif", "jp2", "jps", "lbm", "max", "miff", "mng", "msp", "nitf", "ota", "pbm", "pc1", "pc2", "pc3", "pcf", "pcx", "pdn", "pgm", "PI1", "PI2", "PI3", "pict", "pct", "pnm", "pns", "ppm", "psb", "psd", "pdd", "psp", "px", "pxm", "pxr", "qfx", "raw", "rle", "sct", "sgi", "rgb", "int", "bw", "tga", "tiff", "tif", "vtf", "xbm", "xcf", "xpm", "3dv", "amf", "ai", "awg", "cgm", "cdr", "cmx", "dxf", "e2d", "egt", "eps", "fs", "gbr", "odg", "svg", "stl", "vrml", "x3d", "sxd", "v2d", "vnd", "wmf", "emf", "art", "xar", "png", "webp", "jxr", "hdp", "wdp", "cur", "ecw", "iff", "lbm", "liff", "nrrd", "pam", "pcx", "pgf", "sgi", "rgb", "rgba", "bw", "int", "inta", "sid", "ras", "sun", "tga"];
        $audioExtensions = ["aac", "aiff", "ape", "au", "flac", "gsm", "it", "m3u", "m4a", "mid", "mod", "mp3", "mpa", "pls", "ra", "s3m", "sid", "wav", "wma", "xm"];
        $videoExtensions = ["3g2", "3gp", "aaf", "asf", "avchd", "avi", "drc", "flv", "m2v", "m4p", "m4v", "mkv", "mng", "mov", "mp2", "mp4", "mpe", "mpeg", "mpg", "mpv", "mxf","nsv","ogg","ogv","ogm","qt","rm","rmvb","roq","srt","svi","vob","webm","wmv","yuv"];

        if($request->hasFile('file') && $request->has('path')){
            $filePath = $request['path'];
            $file = $request->file;
            $ext = pathinfo($file->getClientOriginalName(), PATHINFO_EXTENSION);
            $fileType = 'image';
            if(in_array(strtolower($ext), $imageExtensions)) $fileType = 'image';
            if(in_array(strtolower($ext), $audioExtensions)) $fileType = 'audio';
            if(in_array(strtolower($ext), $videoExtensions)) $fileType = 'video';

            $store = "";
            if($fileType == 'image'){
                $store = storeImage()->file($file, $filePath);
            }else{
                $filename = $file->getClientOriginalName();
                $path_parts = pathinfo($filename);
                $filename =  $path_parts['filename'];
                $extension =  $path_parts['extension'];
                $exists = file_exists( public_path() . '/' . $filePath . $filename . '.'  . $extension);
                $storeFileName =  $filename . '.'  . $extension;
                $i = 0;
                while($exists){
                    $i++;
                    $storeFileName =  $filename . "({$i})" . '.'  . $extension;
                    $exists = file_exists( public_path() . '/' . $filePath . $storeFileName);
                }

                $store = $file->move(
                            base_path() . '/public/'  . $filePath ,
                            $storeFileName
                        );
                if($store)
                    $store = $storeFileName;
                else
                    $store = "";
            }

            if($store !==""){
                $path_parts = pathinfo($store);
                $filename =  $path_parts['filename'];
                $extension =  $path_parts['extension'];
                $newFile = $this->file->create([
                                'title'   => $request->has('title')  ? $request['title'] : '',
                                'alt'   => $request->has('alt')  ? $request['alt'] : '',
                                'path' => $filePath,
                                'filename' => $filename . '.' . $extension,
                                'basename'  => $filename,
                                'extension' => $extension,
                                'type' => $fileType,
                                'created_at'    => date('Y-m-d H:i:s')
                        ]);

                $response = responseGenerator()->success(new FileCollection($newFile));
                return response()->json($response['data'], $response['status']);
            }
        }

    }


    public function update(Request $request){
        $validator = Validator::make($request->all(), [
            'id'         => 'required|exists:files,id',
            'title'      => 'nullable',
            'alt'        => 'nullable',
        ]);

        if ($validator->fails()) {
            $errors = $validator->errors();
            $response = responseGenerator()->unprocessableEntity($errors->toArray(), true);
            return response()->json($response['data'], $response['status']);
        }

        $findFile = $this->file->find($request['id']);
        $findFile->update($request->toArray());
        $response  = responseGenerator()->success(new FileCollection($findFile));
        return response()->json($response['data'], $response['status']);

    }



    /**
     * delete file function
     *
     * @param Request $request
     * @param Integer $id
     * @return Json
     */
    public function delete(Request $request, $id){
        $validator = Validator::make(['id' => $id], [
            'id' => 'required|exists:files,id',
        ]);

        if ($validator->fails()) {
            $errors = $validator->errors();
            $response = responseGenerator()->unprocessableEntity($errors->toArray(), true);
            return response()->json($response['data'], $response['status']);
        }

        $files = DB::table('fileables')->where("file_id", $id)->get();

        $force_delete = $request->has('force-delete') ? $request->get('force-delete') : false;
        if($files->count()){
            if(!$force_delete){
                $response = [
                    'cant_delete_file' => __("validation.cant_delete_file", ['attribute' => $files->count()])
                ];
                $response = responseGenerator()->forbidden($response);
                return response()->json($response['data'], $response['status']);
            }
            DB::table('fileables')->where("file_id", $id)->delete();
        }
        $findFile = $this->file->find($id);
        $findFile->delete();
        $response = ['message' => __('messages.successfully_deleted')];
        $response  = responseGenerator()->success($response);
        return response()->json($response['data'], $response['status']);
    }
}

