<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bank extends Model
{
    use HasFactory;

    public function country()
    {
        return $this->belongsTo(Country::class, 'country_id');
    }

    public function fetchAllBanks()
    {
        return self::with(['country','country.currency'])
            ->orderByDesc('id')
            ->get();
    }

    public function fetchAllBanksByCountryId(int $country_id)
    {
        return self::with(['country','country.currency'])
            ->where('country_id', $country_id)
            ->orderByDesc('id')
            ->get();
    }

    /**
     * The list of all banks
     * @return array[]
     */
    public function bankList(): array
    {
        return $banks = [
            ['title' => 'ABBEY MORTAGE BANK', 'country_id' => Country::getCountryByName(Country::$NAME)->id],
            ['title' => 'ABU MICROFINANCE BANK', 'country_id' => Country::getCountryByName(Country::$NAME)->id],
            ['title' => 'ACCESS BANK PLC', 'country_id' => Country::getCountryByName(Country::$NAME)->id],
            ['title' => 'AccessMobile', 'country_id' => Country::getCountryByName(Country::$NAME)->id],
            ['title' => 'ACCION MICROFINANCE BANK', 'country_id' => Country::getCountryByName(Country::$NAME)->id],
            ['title' => 'ADDOSSER MICROFINANCE BANK', 'country_id' => Country::getCountryByName(Country::$NAME)->id],
            ['title' => 'AG MORTAGE BANK PLC', 'country_id' => Country::getCountryByName(Country::$NAME)->id],
            ['title' => 'AL-BARAKAH MICROFINANCE BANK', 'country_id' => Country::getCountryByName(Country::$NAME)->id],
            ['title' => 'Alpha Kapital MICROFINANCE BANK', 'country_id' => Country::getCountryByName(Country::$NAME)->id],
            ['title' => 'AMJU UNIQUE MICROFINANCE BANK', 'country_id' => Country::getCountryByName(Country::$NAME)->id],
            ['title' => 'AMML MFB', 'country_id' => Country::getCountryByName(Country::$NAME)->id],
            ['title' => 'APEKS MICROFINANCE BANK', 'country_id' => Country::getCountryByName(Country::$NAME)->id],
            ['title' => 'ASO SAVINGS AND LOANS', 'country_id' => Country::getCountryByName(Country::$NAME)->id],
            ['title' => 'ASTRAPOLARIS MICROFINANCE BANK', 'country_id' => Country::getCountryByName(Country::$NAME)->id],
            ['title' => 'BAINES CREDIT MICROFINANCE BANK', 'country_id' => Country::getCountryByName(Country::$NAME)->id],
            ['title' => 'BAOBAB MICROFINANCE BANK', 'country_id' => Country::getCountryByName(Country::$NAME)->id],
            ['title' => 'BC KASH MICROFINANCE BANK', 'country_id' => Country::getCountryByName(Country::$NAME)->id],
            ['title' => 'BOCTRUST MICROFINANCE BANK', 'country_id' => Country::getCountryByName(Country::$NAME)->id],
            ['title' => 'BOSAK MICROFINANCE BANK', 'country_id' => Country::getCountryByName(Country::$NAME)->id],
            ['title' => 'BOWEN MICROFINANCE BANK', 'country_id' => Country::getCountryByName(Country::$NAME)->id],
            ['title' => 'BRENT MORTGAGE BANK', 'country_id' => Country::getCountryByName(Country::$NAME)->id],
            ['title' => 'CELLULANT', 'country_id' => Country::getCountryByName(Country::$NAME)->id],
            ['title' => 'CEMCS MICROFINANCE BANK', 'country_id' => Country::getCountryByName(Country::$NAME)->id],
            ['title' => 'CENTRAL BANK OF NIGERIA', 'country_id' => Country::getCountryByName(Country::$NAME)->id],
            ['title' => 'CHAMSMOBILE', 'country_id' => Country::getCountryByName(Country::$NAME)->id],
            ['title' => 'CHIKUM MICROFINANCE BANK', 'country_id' => Country::getCountryByName(Country::$NAME)->id],
            ['title' => 'CIT MICROFINANCE BANK', 'country_id' => Country::getCountryByName(Country::$NAME)->id],
            ['title' => 'CITIBANK NIGERIA LTD', 'country_id' => Country::getCountryByName(Country::$NAME)->id],
            ['title' => 'CONSUMER MICROFINANCE BANK', 'country_id' => Country::getCountryByName(Country::$NAME)->id],
            ['title' => 'CONTEC GLOBAL INFOTECH LTD (NOWNOW)', 'country_id' => Country::getCountryByName(Country::$NAME)->id],
            ['title' => 'CORONATION MERCHANT BANK', 'country_id' => Country::getCountryByName(Country::$NAME)->id],
            ['title' => 'COVENANT', 'country_id' => Country::getCountryByName(Country::$NAME)->id],
            ['title' => 'CREDIT AFRIQUE MICROFINANCE BANK', 'country_id' => Country::getCountryByName(Country::$NAME)->id],
            ['title' => 'DAYLIGHT MICROFINANCE BANK', 'country_id' => Country::getCountryByName(Country::$NAME)->id],
            ['title' => 'DIAMOND (ACCESS BANK PLC)', 'country_id' => Country::getCountryByName(Country::$NAME)->id],
            ['title' => 'EARTHOLEUM', 'country_id' => Country::getCountryByName(Country::$NAME)->id],
            ['title' => 'E-BARCS MICROFINANCE BANK', 'country_id' => Country::getCountryByName(Country::$NAME)->id],
            ['title' => 'ECOBANK NIGERIA PLC', 'country_id' => Country::getCountryByName(Country::$NAME)->id],
            ['title' => 'ECOMOBILE', 'country_id' => Country::getCountryByName(Country::$NAME)->id],
            ['title' => 'EKONDO MICROFINANCE BANK', 'country_id' => Country::getCountryByName(Country::$NAME)->id],
            ['title' => 'EMPIRE TRUST BANK', 'country_id' => Country::getCountryByName(Country::$NAME)->id],
            ['title' => 'ENTERPRISE BANK', 'country_id' => Country::getCountryByName(Country::$NAME)->id],
            ['title' => 'ESAN MICROFINANCE BANK', 'country_id' => Country::getCountryByName(Country::$NAME)->id],
            ['title' => 'ESO-E MICROFINANCE BANK', 'country_id' => Country::getCountryByName(Country::$NAME)->id],
            ['title' => 'ETRANZACT', 'country_id' => Country::getCountryByName(Country::$NAME)->id],
            ['title' => 'FAST MICROFINANCE BANK', 'country_id' => Country::getCountryByName(Country::$NAME)->id],
            ['title' => 'FBN MORTGAGES LIMITED', 'country_id' => Country::getCountryByName(Country::$NAME)->id],
            ['title' => 'FBNMOBILE', 'country_id' => Country::getCountryByName(Country::$NAME)->id],
            ['title' => 'FBNQUEST MERCHANT BANK LIMITED', 'country_id' => Country::getCountryByName(Country::$NAME)->id],
            ['title' => 'FCMB', 'country_id' => Country::getCountryByName(Country::$NAME)->id],
            ['title' => 'FCMB EASY ACCOUNT', 'country_id' => Country::getCountryByName(Country::$NAME)->id],
            ['title' => 'FET', 'country_id' => Country::getCountryByName(Country::$NAME)->id],
            ['title' => 'FFS MICROFINANCE BANK', 'country_id' => Country::getCountryByName(Country::$NAME)->id],
            ['title' => 'FIDELITY BANK PLC', 'country_id' => Country::getCountryByName(Country::$NAME)->id],
            ['title' => 'FIDELITY MOBILE', 'country_id' => Country::getCountryByName(Country::$NAME)->id],
            ['title' => 'FIDFUND MFB', 'country_id' => Country::getCountryByName(Country::$NAME)->id],
            ['title' => 'FINATRUST MICROFINANCE BANK', 'country_id' => Country::getCountryByName(Country::$NAME)->id],
            ['title' => 'FIRST BANK OF NIGERIA PLC', 'country_id' => Country::getCountryByName(Country::$NAME)->id],
            ['title' => 'FIRST GENERATION MORTGAGE BANK', 'country_id' => Country::getCountryByName(Country::$NAME)->id],
            ['title' => 'FIRST ROYAL MICROFINANCE BANK', 'country_id' => Country::getCountryByName(Country::$NAME)->id],
            ['title' => 'FLUTTERWAVE TECHNOLOGY SOLUTIONS LIMITED', 'country_id' => Country::getCountryByName(Country::$NAME)->id],
            ['title' => 'FORTIS MICROFINANCE BANK', 'country_id' => Country::getCountryByName(Country::$NAME)->id],
            ['title' => 'FSDH MERCHANT BANK LIMIT', 'country_id' => Country::getCountryByName(Country::$NAME)->id],
            ['title' => 'FULLRANGE MICROFINANCE BANK', 'country_id' => Country::getCountryByName(Country::$NAME)->id],
            ['title' => 'FUTO MICROFINANCE BANK', 'country_id' => Country::getCountryByName(Country::$NAME)->id],
            ['title' => 'GASHUA MICROFINANCE BANK', 'country_id' => Country::getCountryByName(Country::$NAME)->id],
            ['title' => 'GATEWAY MORTGAGE BANK', 'country_id' => Country::getCountryByName(Country::$NAME)->id],
            ['title' => 'GOMONEY', 'country_id' => Country::getCountryByName(Country::$NAME)->id],
            ['title' => 'GOWANS MFB', 'country_id' => Country::getCountryByName(Country::$NAME)->id],
            ['title' => 'GREENBANK MICROFINANCE BANK', 'country_id' => Country::getCountryByName(Country::$NAME)->id],
            ['title' => 'GROOMING MICROFINANCE BANK', 'country_id' => Country::getCountryByName(Country::$NAME)->id],
            ['title' => 'GTMOBILE', 'country_id' => Country::getCountryByName(Country::$NAME)->id],
            ['title' => 'GUARANTY TRUST BANK PLC', 'country_id' => Country::getCountryByName(Country::$NAME)->id],
            ['title' => 'HAGGAI MORTGAGE BANK LIMITED', 'country_id' => Country::getCountryByName(Country::$NAME)->id],
            ['title' => 'HASAL MICROFINANCE BANK', 'country_id' => Country::getCountryByName(Country::$NAME)->id],
            ['title' => 'HEDONMARK', 'country_id' => Country::getCountryByName(Country::$NAME)->id],
            ['title' => 'HERITAGE BANK', 'country_id' => Country::getCountryByName(Country::$NAME)->id],
            ['title' => 'HIGHTSTREET MICROFINANCE BANK', 'country_id' => Country::getCountryByName(Country::$NAME)->id],
            ['title' => 'IBILE MFB', 'country_id' => Country::getCountryByName(Country::$NAME)->id],
            ['title' => 'IMPERIAL HOMES MORTGAGE BANK', 'country_id' => Country::getCountryByName(Country::$NAME)->id],
            ['title' => 'INFINITY MICROFINANCE BANK', 'country_id' => Country::getCountryByName(Country::$NAME)->id],
            ['title' => 'INFINITY TRUST MORTGAGE BANK', 'country_id' => Country::getCountryByName(Country::$NAME)->id],
            ['title' => 'INNOVECTIVES KESH', 'country_id' => Country::getCountryByName(Country::$NAME)->id],
            ['title' => 'INTELLIFIN', 'country_id' => Country::getCountryByName(Country::$NAME)->id],
            ['title' => 'IRL MICROFINANCE BANK', 'country_id' => Country::getCountryByName(Country::$NAME)->id],
            ['title' => 'JAIZ BANK PLC', 'country_id' => Country::getCountryByName(Country::$NAME)->id],
            ['title' => 'JUBILEE LIFE MORTGAGE BANK', 'country_id' => Country::getCountryByName(Country::$NAME)->id],
            ['title' => 'KCMB MICROFINANCE BANK', 'country_id' => Country::getCountryByName(Country::$NAME)->id],
            ['title' => 'KEGOW', 'country_id' => Country::getCountryByName(Country::$NAME)->id],
            ['title' => 'KEYSTONE BANK', 'country_id' => Country::getCountryByName(Country::$NAME)->id],
            ['title' => 'KUDIMONEY MICROFINANCE BANK', 'country_id' => Country::getCountryByName(Country::$NAME)->id],
            ['title' => 'LA FAYETTE MICROFINANCE BANK', 'country_id' => Country::getCountryByName(Country::$NAME)->id],
            ['title' => 'LAGOS BUILDING INVESTMENT COMPANY', 'country_id' => Country::getCountryByName(Country::$NAME)->id],
            ['title' => 'LAPO MICROFINANCE BANK', 'country_id' => Country::getCountryByName(Country::$NAME)->id],
            ['title' => 'MAINSTREET MFB', 'country_id' => Country::getCountryByName(Country::$NAME)->id],
            ['title' => 'MALACHY MICROFINANCE BANK', 'country_id' => Country::getCountryByName(Country::$NAME)->id],
            ['title' => 'MICROVIS MICROFINANCE BANK', 'country_id' => Country::getCountryByName(Country::$NAME)->id],
            ['title' => 'MIDLAND MICROFINANCE BANK', 'country_id' => Country::getCountryByName(Country::$NAME)->id],
            ['title' => 'MKUDI', 'country_id' => Country::getCountryByName(Country::$NAME)->id],
            ['title' => 'MONEY TRUST MICROFINANCE BANK', 'country_id' => Country::getCountryByName(Country::$NAME)->id],
            ['title' => 'MONEYBOX', 'country_id' => Country::getCountryByName(Country::$NAME)->id],
            ['title' => 'MUTUAL BENEFITS MICROFINANCE BANK', 'country_id' => Country::getCountryByName(Country::$NAME)->id],
            ['title' => 'MUTUAL TRUST MICROFINANCE BANK', 'country_id' => Country::getCountryByName(Country::$NAME)->id],
            ['title' => 'NAGARTA MICROFINANCE BANK', 'country_id' => Country::getCountryByName(Country::$NAME)->id],
            ['title' => 'NDIORAH MICROFINANCE BANK', 'country_id' => Country::getCountryByName(Country::$NAME)->id],
            ['title' => 'NEW PRUDENTIAL BANK', 'country_id' => Country::getCountryByName(Country::$NAME)->id],
            ['title' => 'NIGERIA INTERBANK SETTLEMENT SYSTEM', 'country_id' => Country::getCountryByName(Country::$NAME)->id],
            ['title' => 'NIP VIRTUAL BANK', 'country_id' => Country::getCountryByName(Country::$NAME)->id],
            ['title' => 'NIRSAL MICROFINANCE BANK', 'country_id' => Country::getCountryByName(Country::$NAME)->id],
            ['title' => 'NOVA MERCHANT BANK LTD', 'country_id' => Country::getCountryByName(Country::$NAME)->id],
            ['title' => 'NPF MICROFINANCE BANK', 'country_id' => Country::getCountryByName(Country::$NAME)->id],
            ['title' => 'OHAFIA MFB', 'country_id' => Country::getCountryByName(Country::$NAME)->id],
            ['title' => 'OKPOGA MICROFINANCE BANK', 'country_id' => Country::getCountryByName(Country::$NAME)->id],
            ['title' => 'OMOLUABI SAVINGS AND LOANS PLC', 'country_id' => Country::getCountryByName(Country::$NAME)->id],
            ['title' => 'ONE FINANCE', 'country_id' => Country::getCountryByName(Country::$NAME)->id],
            ['title' => 'PAGA', 'country_id' => Country::getCountryByName(Country::$NAME)->id],
            ['title' => 'PAGE MFBANK', 'country_id' => Country::getCountryByName(Country::$NAME)->id],
            ['title' => 'PARALLEX MFB', 'country_id' => Country::getCountryByName(Country::$NAME)->id],
            ['title' => 'PARKWAY-READYCASH', 'country_id' => Country::getCountryByName(Country::$NAME)->id],
            ['title' => 'PAYATTITUDE ONLINE', 'country_id' => Country::getCountryByName(Country::$NAME)->id],
            ['title' => 'PAYCOM (OPAY)', 'country_id' => Country::getCountryByName(Country::$NAME)->id],
            ['title' => 'PECAN TRUST MICROFINANCE BANK', 'country_id' => Country::getCountryByName(Country::$NAME)->id],
            ['title' => 'PENNYWISE MICROFINANCE BANK', 'country_id' => Country::getCountryByName(Country::$NAME)->id],
            ['title' => 'PLATINUM MORTGAGE BANK', 'country_id' => Country::getCountryByName(Country::$NAME)->id],
            ['title' => 'POLARIS BANK LTD (SKY BANK PLC)', 'country_id' => Country::getCountryByName(Country::$NAME)->id],
            ['title' => 'PROVIDUS BANK', 'country_id' => Country::getCountryByName(Country::$NAME)->id],
            ['title' => 'QUICKFUND MICROFINANCE BANK', 'country_id' => Country::getCountryByName(Country::$NAME)->id],
            ['title' => 'RAHAMA MICROFINANCE BANK', 'country_id' => Country::getCountryByName(Country::$NAME)->id],
            ['title' => 'RAND MERCHANT BANK', 'country_id' => Country::getCountryByName(Country::$NAME)->id],
            ['title' => 'REFUGE MORTGAGE BANK', 'country_id' => Country::getCountryByName(Country::$NAME)->id],
            ['title' => 'REGENT MFB', 'country_id' => Country::getCountryByName(Country::$NAME)->id],
            ['title' => 'RELIANCE MICROFINANCE BANK', 'country_id' => Country::getCountryByName(Country::$NAME)->id],
            ['title' => 'RENMONEY MICROFINANCE BANK', 'country_id' => Country::getCountryByName(Country::$NAME)->id],
            ['title' => 'RICHWAY MICROFINANCE BANK', 'country_id' => Country::getCountryByName(Country::$NAME)->id],
            ['title' => 'ROYAL EXCHANGE MICROFINANCE BANK', 'country_id' => Country::getCountryByName(Country::$NAME)->id],
            ['title' => 'SAFETRUST MORTGAGE BANK', 'country_id' => Country::getCountryByName(Country::$NAME)->id],
            ['title' => 'SAGAMU MICROFINANCE BANK LTD', 'country_id' => Country::getCountryByName(Country::$NAME)->id],
            ['title' => 'SEED CAPITAL MICROFINANCE BANK', 'country_id' => Country::getCountryByName(Country::$NAME)->id],
            ['title' => 'STANBIC IBTC BANK PLC', 'country_id' => Country::getCountryByName(Country::$NAME)->id],
            ['title' => 'STANBIC IBTC VIRTUAL BANKING', 'country_id' => Country::getCountryByName(Country::$NAME)->id],
            ['title' => 'STANDARD CHARTERED BANK NIGERIA LTD', 'country_id' => Country::getCountryByName(Country::$NAME)->id],
            ['title' => 'STANFORD MICROFINANCE BANK', 'country_id' => Country::getCountryByName(Country::$NAME)->id],
            ['title' => 'STELLAS MICROFINANCE BANK', 'country_id' => Country::getCountryByName(Country::$NAME)->id],
            ['title' => 'STERLING BANK PLC', 'country_id' => Country::getCountryByName(Country::$NAME)->id],
            ['title' => 'SUNTRUST BANK', 'country_id' => Country::getCountryByName(Country::$NAME)->id],
            ['title' => 'TAGPAY', 'country_id' => Country::getCountryByName(Country::$NAME)->id],
            ['title' => 'TCF', 'country_id' => Country::getCountryByName(Country::$NAME)->id],
            ['title' => 'TEASYMOBILE', 'country_id' => Country::getCountryByName(Country::$NAME)->id],
            ['title' => 'TRIDENT MICROFINANCE BANK', 'country_id' => Country::getCountryByName(Country::$NAME)->id],
            ['title' => 'TRUSTBOND MORTGAGE BANK', 'country_id' => Country::getCountryByName(Country::$NAME)->id],
            ['title' => 'UNICAL MICROFINANCE BANK', 'country_id' => Country::getCountryByName(Country::$NAME)->id],
            ['title' => 'UNION BANK OF NIGERIA PLC', 'country_id' => Country::getCountryByName(Country::$NAME)->id],
            ['title' => 'UNITED BANK FOR AFRICA PLC', 'country_id' => Country::getCountryByName(Country::$NAME)->id],
            ['title' => 'UNITY BANK PLC', 'country_id' => Country::getCountryByName(Country::$NAME)->id],
            ['title' => 'UNN MICROFINANCE BANK', 'country_id' => Country::getCountryByName(Country::$NAME)->id],
            ['title' => 'VERITE MICROFINANCE BANK', 'country_id' => Country::getCountryByName(Country::$NAME)->id],
            ['title' => 'VFD MICROFINANCE BANK', 'country_id' => Country::getCountryByName(Country::$NAME)->id],
            ['title' => 'VISA MICROFINANCE BANK', 'country_id' => Country::getCountryByName(Country::$NAME)->id],
            ['title' => 'VISUAL ICT', 'country_id' => Country::getCountryByName(Country::$NAME)->id],
            ['title' => 'VTNETWORKS', 'country_id' => Country::getCountryByName(Country::$NAME)->id],
            ['title' => 'WEMA BANK PLC', 'country_id' => Country::getCountryByName(Country::$NAME)->id],
            ['title' => 'WETLAND MFB', 'country_id' => Country::getCountryByName(Country::$NAME)->id],
            ['title' => 'XSLNCE MICROFINANCE BANK', 'country_id' => Country::getCountryByName(Country::$NAME)->id],
            ['title' => 'YES MICROFINANCE BANK', 'country_id' => Country::getCountryByName(Country::$NAME)->id],
            ['title' => 'ZENITH BANK PLC', 'country_id' => Country::getCountryByName(Country::$NAME)->id],
            ['title' => 'ZENITHMOBILE', 'country_id' => Country::getCountryByName(Country::$NAME)->id],
            ['title' => 'ZINTERNET NIGERIA LIMITED', 'country_id' => Country::getCountryByName(Country::$NAME)->id]
        ];
    }

    public function ghanaBanks(): array
    {
        return $ghanaBanks = [
            [
                'title' => 'ABSA BANK GHANA LIMITED',
                'country_id' => 2
            ],
            [
                'title' => 'ACCESS BANK GHANA PLC',
                'country_id' => 2
            ],
            [
                'title' => 'AGRICULTURAL DEVELOPMENT BANK OF GHANA',
                'country_id' => 2
            ],
            [
                'title' => 'ARB APEX BANK GHANA LIMITED',
                'country_id' => 2
            ],
            [
                'title' => 'BANK OF AFRICA GHANA LIMITED',
                'country_id' => 2
            ],
            [
                'title' => 'Bank of Beirut',
                'country_id' => 2
            ],
            [
                'title' => 'CalBank Limited',
                'country_id' => 2
            ],
            [
                'title' => 'Consolidated Bank Ghana Limited',
                'country_id' => 2
            ],
            [
                'title' => 'Citibank N.A. Ghana',
                'country_id' => 2
            ],
            [
                'title' => 'Ecobank (Ghana) Limited',
                'country_id' => 2
            ],
            [
                'title' => 'Exim Bank of Korea',
                'country_id' => 2
            ],
            [
                'title' => 'First Bank of Nigeria (Ghana) Limited',
                'country_id' => 2
            ],
            [
                'title' => 'Fidelity Bank Ghana Limited',
                'country_id' => 2
            ],
            [
                'title' => 'First Atlantic Bank Limited',
                'country_id' => 2
            ],
            [
                'title' => 'First National Bank Ghana',
                'country_id' => 2
            ],
            [
                'title' => 'GCB Bank Limited',
                'country_id' => 2
            ],
            [
                'title' => 'Ghana International Bank Plc',
                'country_id' => 2
            ],
            [
                'title' => 'Guaranty Trust Bank Ghana Limited',
                'country_id' => 2
            ],
            [
                'title' => 'National Investment Bank Limited',
                'country_id' => 2
            ],
            [
                'title' => 'OmniBSIC Bank Ghana Limited',
                'country_id' => 2
            ],
            [
                'title' => 'Prudential Bank',
                'country_id' => 2
            ],
            [
                'title' => 'Republic Bank Ghana Limited',
                'country_id' => 2
            ],
            [
                'title' => 'Société Générale Ghana Limited',
                'country_id' => 2
            ],
            [
                'title' => 'Stanbic Bank Ghana Limited',
                'country_id' => 2
            ],
            [
                'title' => 'Standard Chartered Bank Ghana Limited',
                'country_id' => 2
            ],
            [
                'title' => 'United Bank for Africa Ghana Limited',
                'country_id' => 2
            ],
            [
                'title' => 'Universal Merchant Bank Limited',
                'country_id' => 2
            ],
            [
                'title' => 'Zenith Bank (Ghana) Limited',
                'country_id' => 2
            ],
        ];
    }

    /**
     * This is used to populate the banks to the database
     */
    public function init(){
        $banks = $this->bankList();

        foreach ($banks as $bank){
            $Rec = new self();
            $Rec->title = strtoupper($bank['title']);
            $Rec->country_id = $bank['country_id'];
            $Rec->save();
        }
    }

    /**
     * This is used to populate Ghana Banks to banks database
     */
    public function initGhanaBank(){
        $ghanaBanks = $this->ghanaBanks();

        foreach ($ghanaBanks as $ghana){
            $Rec = new self();
            $Rec->title = strtoupper($ghana['title']);
            $Rec->country_id = $ghana['country_id'];
            $Rec->save();
        }
    }

    /**
     * @return array
     */
    public static function getBankTitles(): array
    {
        $result = [];
        $banks = self::all();

        $banks->each(function ($bank) use (&$result){
            $result[$bank->title] = $bank->title;
        });

        return $result;
    }

    /**
     * @return array
     */
    public static function getAllBanks(): array
    {
        $result = [];
        self::orderBy('id')->chunk(50, function ($banks) use(&$result)
        {
            foreach ($banks as $bank)
            {
                unset($bank->bank_code, $bank->created_at, $bank->updated_at, $bank->id);
                $result[] = $bank;
            }
        });

        return $result;
    }

    /**
     * @return array
     */
    public static function getBanksByCountryId(int $country_id): array
    {
        $result = [];
        self::where('country_id', $country_id)
            ->orderBy('id')->chunk(50, function ($banks) use(&$result)
            {
                foreach ($banks as $bank)
                {
                    unset($bank->bank_code, $bank->created_at, $bank->updated_at, $bank->id);
                    $result[] = $bank;
                }
            });

        return $result;
    }

    public static function migrateBank()
    {
        (new Bank)->init();
//        (new Bank)->initGhanaBank();
    }
}
