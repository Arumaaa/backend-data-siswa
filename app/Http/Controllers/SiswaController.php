<?php

namespace App\Http\Controllers;

use App\Models\Siswa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SiswaController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data = Siswa::all();
        if ($data) {
            return response()->json([
                'success'   => true,
                'message'   => 'Menampilkan Data-data Siswa',
                'data'      => $data
            ], 200);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Data Siswa Tidak Ditemukan!',
            ], 404);
        }
        
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
         $valid = Validator::make($request->all(), [
            'nama'   => 'required | unique:siswas',
            'kelas' => 'required',
        ]);

        if ($valid->fails()) {

            return response()->json([
                'success' => false,
                'message' => 'Kolom belum di isi atau Nama tersebut sudah ada',
                'data'   => $valid->errors()
            ],401);

        } else {

            $post = Siswa::whereId($request)->create([
                'nama' => $request->input('nama'),
                'kelas'   => $request->input('kelas'),
            ]);

            if ($post) {
                return response()->json([
                    'success' => true,
                    'message' => 'Data Siswa berhasil di buat',
                    'data' => $post
                ], 201);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => "Data Siswa Gagal di buat",
                ], 400);
            }

            }
            }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Siswa  $siswa
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $datasiswa = Siswa::where('id',$id)->get();
           return response()->json([
            'success' => true,
            'message' => "Menampilkan Data Siswa Dengan ID($id)",
            'data' => $datasiswa
        ]);

        
    
        
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Siswa  $siswa
     * @return \Illuminate\Http\Response
     */
    public function edit(Siswa $siswa)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Siswa  $siswa
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id )
    {
        $valid = Validator::make($request->all(), [
            'nama'   => 'required | unique:siswas',
            'kelas' => 'required',
        ]);
        
        
    
        if ($valid->fails()) {
    
            return response()->json([
                'success' => false,
                'message' => 'Kolom belum di isi atau Nama sudah ada',
                'data'   => $valid->errors()
            ],401);
            
    
        } else {
    
            $post = Siswa::whereId($id)->update([
                'nama' => $request->input('nama'),
                'kelas'   => $request->input('kelas'),
            ]);
    
            if ($post) {
                return response()->json([
                    'success' => true,
                    'message' => "Data Siswa dengan ID($id) Berhasil di Update",
                    'data' => $id
                ], 201);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => "Data dengan ID:$id Gagal diUpdate!",
                ], 400);
            }
    
        }

       
    }

    /**
     * Remove the specified resource from storage.
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Siswa  $siswa
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request,$id)
    {
        $post = Siswa::where('id',$id)->first();

            if ($post != null) {
                $post -> delete();
                return response()->json([
                    'success' => true,
                    'message' => "Data siswa berhasil di hapus",
                    'data' => $id
                ], 200);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => "Data siswa dengan ID : $id gagal di hapus atau sudah di hapus",
                ], 400);
            }
    }
}
