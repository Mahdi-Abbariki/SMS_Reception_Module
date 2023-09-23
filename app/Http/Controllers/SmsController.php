<?php

namespace App\Http\Controllers;

use App\Library\SMS\SmsProvider;
use App\Models\SmsConfig;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Validation\Rule;

class SmsController extends Controller
{
    /**
     * return a list of all available providers and all configs that has been set
     * 
     * @return Illuminate\Http\Response
     */
    public function index()
    {
        $providers = SmsConfig::all();
        return response()->json([
            "providers" => $providers,
            "availableProviders" => SmsProvider::$providerNames,
        ]);
    }

    /**
     * @param Request $request
     * 
     * @return Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            "provider" => ["required", "unique:sms_configs,provider", Rule::in(array_keys(SmsProvider::$providerNames))],
            "username" => "required_without:apiKey",
            "password" => "required_without:apiKey",
            "apiKey" => "required_without_all:username,password",
        ]);

        $config = new SmsConfig();
        $config->provider = $request->provider;
        $config->username = $request->username;
        $config->password = encrypt($request->password);
        $config->api_key  = $request->apiKey;
        $config->sender_number  = $request->senderNumber;
        // activate the first config that is being created so we always have one config activated
        $config->active  = !SmsConfig::query()->where("active", 1)->count();
        $config->save();

        return response()->noContent();
    }

    /**
     * update the data of specified provider
     * 
     * @param Request $request
     * @param string $providerName the identifier of that provider
     * 
     * @return Illuminate\Http\Response
     */
    public function update(Request $request, $providerName)
    {
        $request->validate([
            "username" => "required_without:apiKey",
            "apiKey" => "required_without_all:username",
        ]);
        $config = SmsConfig::query()
            ->where("provider", $providerName)
            ->firstOrFail();
        $config->username = $request->username;
        if ($request->password)
            $config->password = encrypt($request->password);
        $config->api_key  = $request->apiKey;
        $config->sender_number  = $request->senderNumber;
        $config->save();

        return response()->noContent();
    }

    /**
     * activate one provider and inactive all others
     * 
     * @param string $providerName
     * 
     * @return Illuminate\Http\Response
     */
    public function activateProvider($providerName)
    {
        $provider = SmsConfig::query()
            ->where("provider", $providerName)
            ->first();

        if (!$provider)
            return response()->json(["errors" => ["config" => ["برای این ارائه دهنده تنظیماتی ذخیره نشده است."]]], 422);

        //inactive all other providers
        SmsConfig::query()
            ->where("active", 1)
            ->where("provider", "!=", $providerName)
            ->each(function ($item) {
                $item->active = 0;
                $item->save();
            });

        $provider->active = 1;
        $provider->save();

        return response()->noContent();
    }


    /**
     * call the sms:updateReceive.
     * to update the unread list of received SMS and send the related response. 
     * 
     * @return Illuminate\Http\Response
     */
    public function updateReceiveSms()
    {
        $countSent = Artisan::call('sms:updateReceive');
        return response()->json([
            "sentSmsCount" => $countSent
        ]);
    }
}
