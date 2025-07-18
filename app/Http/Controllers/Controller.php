<?php

namespace App\Http\Controllers;

use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;

abstract class Controller
{
    /**
     * Verifica si el usuario autenticado es administrador.
     *
     * @param  \App\Models\User  $user
     * @return bool
     */
    protected function isAdmin($user)
    {
        return $user->roles()->where('code', 'admin')->exists();
    }

    protected function getFileSystem(): Filesystem
    {
        return Storage::disk('ftps');
    }

    protected function uploadToFtp($filename, $file): JsonResponse|bool
    {
        $fileSize = $file->getSize();


        $ftpHost = env('FTP_HOST');
        $ftpUser = env('FTP_USERNAME');
        $ftpPass = env('FTP_PASSWORD');
        $ftpRoot = env('FTP_ROOT');
        $remotePath = $ftpRoot . $filename;

        $ftpConnection = ftp_ssl_connect($ftpHost);
        if (!$ftpConnection) {
            return response()->json(['error' => 'No connection'], 500);
        }

        $login = ftp_login($ftpConnection, $ftpUser, $ftpPass);
        if (!$login) {
            ftp_close($ftpConnection);
            return response()->json(['error' => 'No login'], 500);
        }

        ftp_pasv($ftpConnection, true); // Modo pasivo

        $stream = fopen($file, 'r');

        $upload = ftp_nb_fput($ftpConnection, $remotePath, $stream, FTP_BINARY);

        while ($upload === FTP_MOREDATA) {
            $uploaded = ftell($stream); // Calcular bytes subidos
            $progress = round(($uploaded / $fileSize) * 100);

            // Aquí puedes enviar el progreso al cliente (por ejemplo, con WebSockets o SSE)
            //$this->sendPublishMessage("ftp_upload_progress_" . $channelId, ["progress" => $progress]);

            // Continuar la subida
            $upload = ftp_nb_continue($ftpConnection);
        }

        fclose($stream);
        ftp_close($ftpConnection);

        if ($upload === FTP_FINISHED) {
            return true;
        }

        return false;
    }
}
