<?php

namespace App\Http\Controllers\Api\V1\App;

use App\Http\Controllers\Controller;
use App\Models\Config;
use App\Traits\ApiResponser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log; 

class ConfigController extends Controller
{
    use ApiResponser;

    /**
     * Get specific configurations by keys.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $request->validate([
            'keys' => 'required|array',
            'keys.*' => 'string'
        ]);

        try {
    
            $keys = $request->input('keys');

            
            $configs = Config::whereIn('key', $keys)->get();

          
            $formattedData = $configs->pluck('value', 'key');

            // Return Success
            return $this->success(
                200, 
                'resp_msg_configs_retrieved', 
                'Configurations retrieved successfully.', 
                $formattedData
            );

        } catch (\Exception $e) {
           
            Log::error('Config Fetch Error: ' . $e->getMessage());

            
            return $this->error(
                500, 
                'resp_msg_server_error', 
                'Terjadi kesalahan pada server saat mengambil konfigurasi. Silakan coba lagi nanti.',
            );
        }
    }
}