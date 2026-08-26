<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*
| -------------------------------------------------------------------
| MIME TYPES OVERRIDE - TahfidzCMS
| -------------------------------------------------------------------
| CodeIgniter 3 memuat daftar mime bawaan dari system/config/mimes.php
| HANYA jika application/config/mimes.php ini tidak ada. Supaya kita
| tidak perlu duplikasi ratusan baris daftar mime bawaan CI3, file ini
| me-load daftar bawaan tsb dulu, lalu menambahkan/menimpa satu entri
| yang kita butuhkan: 'webm'.
|
| Kenapa perlu: rekaman audio bukti setoran (lihat views/setoran/form.php
| & libraries/Upload_handler.php) dibuat browser lewat MediaRecorder API,
| yang hasilnya berformat WebM dengan MIME asli 'audio/webm'. Daftar
| mimes.php bawaan CI3 hanya memetakan ekstensi 'webm' ke 'video/webm',
| sehingga validasi MIME di CI3 Upload library akan MENOLAK file audio
| webm kita walau ekstensinya sudah diizinkan di Upload_handler.
|
| Baris di bawah menambahkan 'audio/webm' sebagai varian MIME yang sah
| untuk ekstensi 'webm', tanpa mengubah perilaku upload video webm biasa.
*/

require_once BASEPATH . 'config/mimes.php';

// $mimes diisi oleh file bawaan CI3 di atas. Timpa/gabungkan entri 'webm'
// supaya menerima baik video maupun audio webm.
$mimes['webm'] = array('video/webm', 'audio/webm');
