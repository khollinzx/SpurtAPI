<?php

namespace App\Models;

use App\Services\Helper;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Country extends Model
{
    use HasFactory;

    public static $NAME = 'Nigeria';
    public static $SLUG = 'NGN';
    public static $CODE = '234';
    public static $DIGITLENGTH = 11;

    protected $hidden = [
        'password',
        'created_at',
        'updated_at',
        'deleted_at'
    ];

    protected $fillable = [
        'name',
        'slug',
        'code',
        'digit_length'
    ];

    public function currency(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Currency::class, 'currency_id');
    }

    public function getId(): int
    {
        return $this->attributes["id"];
    }

    public function getName(): string
    {
        return $this->attributes["name"];
    }

    public function getSlug(): string
    {
        return $this->attributes["slug"];
    }

    public function getMobileLength(): int
    {
        return $this->attributes["digit_length"];
    }

    public function getCode(): int
    {
        return $this->attributes["code"];
    }

    public function getCurrency(): Currency
    {
        return $this->currency;
    }

    /**
     * finds a Admin User by login credentials
     * @param string $column
     * @param string $value
     * @return mixed
     */
    public static function findByColumnAndValue($column, $value)
    {
        return self::where($column, $value)
            ->first();
    }

    public static function checkIfExistElseWhere(string $column, string $field, int $id)
    {
        return self::where($column, $field)
            ->where("id", "!=", $id)
            ->first();
    }

    /**check if a user with the username exist
     * @param string $name
     * @return mixed
     */
    public static function getCountryByName(string $name)
    {
        return self::with(['currency'])
            ->where('name',$name)->first();
    }

    /**
     * This is initializes a default user
     */
    public static function initCountry()
    {
        if(!self::getCountryByName(self::$NAME))
        {
            $Status = new self();
            $Status->name = ucwords(self::$NAME);
            $Status->slug = ucwords(self::$SLUG);
            $Status->code = strtolower(self::$CODE);
            $Status->digit_length = self::$DIGITLENGTH;
            $Status->currency_id = Currency::getCurrencyByName(Currency::$NAME)->id;
            $Status->save();
        }
    }

    public function initializeNewCountry(array $validated)
    {
        $checker = self::getCountryByName($validated["name"]);
        if(! $checker)
            return Helper::runModelCreation(new self(), [
                'name' => $validated["name"],
                'slug' => $validated["slug"],
                'slug' => $validated["code"],
                'digit_length' => $validated["digit_length"],
                'currency_id' => $validated["currency_id"]
            ]);
    }

    /** Finds a country name by Id
     * @param int $countryId
     * @return mixed
     */
    public static function findCountryById(int $countryId)
    {
        return self::with(['currency'])
            ->where('id', $countryId)
            ->first();
    }

    /**Fetches all country
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public static function fetchAllCountry(): \Illuminate\Database\Eloquent\Collection
    {
        return self::with(['currency'])
            ->orderByDesc('id')
            ->get();
    }

    /**
     * @param Model $model
     * @param string $name
     * @return Model
     */
    public function updateCountryWhereExist(Model $model, array $fields):Model
    {
        return Helper::runModelUpdate($model, [
                'name' => $fields["name"],
                'slug' => $fields["slug"],
                'slug' => $fields["code"],
                'digit_length' => $fields["digit_length"],
                'currency_id' => $fields["currency_id"]
            ]);
    }
    public static function runSQL()
    {
        Country::truncate();

        $countries = [
            ['name' => 'Afghanistan', 'slug' => 'AF', 'code' => 93, 'digit_length' => 9],
            ['name' => 'Åland Islands', 'slug' => 'AX', 'code' => 358, 'digit_length' => 10],
            ['name' => 'Albania', 'slug' => 'AL', 'code' => 355, 'digit_length' => 10],
            ['name' => 'Algeria', 'slug' => 'DZ', 'code' => 213, 'digit_length' => 9],
            ['name' => 'American Samoa', 'slug' => 'AS', 'code' => 1684, 'digit_length' => 10],
            ['name' => 'Andorra', 'slug' => 'AD', 'code' => 376, 'digit_length' => 10],
            ['name' => 'Angola', 'slug' => 'AO', 'code' => 244, 'digit_length' => 10],
            ['name' => 'Anguilla', 'slug' => 'AI', 'code' => 1264, 'digit_length' => 10],
            ['name' => 'Antarctica', 'slug' => 'AQ', 'code' => 0, 'digit_length' => 10],
            ['name' => 'Antigua and Barbuda', 'slug' => 'AG', 'code' => 1268, 'digit_length' => 10],
            ['name' => 'Argentina', 'slug' => 'AR', 'code' => 54, 'digit_length' => 10],
            ['name' => 'Armenia', 'slug' => 'AM', 'code' => 374, 'digit_length' => 6],
            ['name' => 'Aruba', 'slug' => 'AW', 'code' => 297, 'digit_length' => 7],
            ['name' => 'Australia', 'slug' => 'AU', 'code' => 61, 'digit_length' => 9],
            ['name' => 'Austria', 'slug' => 'AT', 'code' => 43, 'digit_length' => 11],
            ['name' => 'Azerbaijan', 'slug' => 'AZ', 'code' => 994, 'digit_length' => 9],
            ['name' => 'Bahamas', 'slug' => 'BS', 'code' => 1242, 'digit_length' => 10],
            ['name' => 'Bahrain', 'slug' => 'BH', 'code' => 973, 'digit_length' => 8],
            ['name' => 'Bangladesh', 'slug' => 'BD', 'code' => 880, 'digit_length' => 10],
            ['name' => 'Barbados', 'slug' => 'BB', 'code' => 1246, 'digit_length' => 10],
            ['name' => 'Belarus', 'slug' => 'BY', 'code' => 375, 'digit_length' => 9],
            ['name' => 'Belgium', 'slug' => 'BE', 'code' => 32, 'digit_length' => 9],
            ['name' => 'Belize', 'slug' => 'BZ', 'code' => 501, 'digit_length' => 7],
            ['name' => 'Benin', 'slug' => 'BJ', 'code' => 229, 'digit_length' => 9],
            ['name' => 'Bermuda', 'slug' => 'BM', 'code' => 1441, 'digit_length' => 10],
            ['name' => 'Bhutan', 'slug' => 'BT', 'code' => 975, 'digit_length' => 10],
            ['name' => 'Bolivia, Plurinational State of', 'slug' => 'BO', 'code' => 591, 'digit_length' => 10],
            ['name' => 'Bonaire, Sint Eustatius and Saba', 'slug' => 'BQ', 'code' => 0, 'digit_length' => 10],
            ['name' => 'Bosnia and Herzegovina', 'slug' => 'BA', 'code' => 387, 'digit_length' => 8],
            ['name' => 'Botswana', 'slug' => 'BW', 'code' => 267, 'digit_length' => 10],
            ['name' => 'Bouvet Island', 'slug' => 'BV', 'code' => 0, 'digit_length' => 10],
            ['name' => 'Brazil', 'slug' => 'BR', 'code' => 55, 'digit_length' => 11],
            ['name' => 'British Indian Ocean Territory', 'slug' => 'IO', 'code' => 246, 'digit_length' => 7],
            ['name' => 'Brunei Darussalam', 'slug' => 'BN', 'code' => 673, 'digit_length' => 10],
            ['name' => 'Bulgaria', 'slug' => 'BG', 'code' => 359, 'digit_length' => 9],
            ['name' => 'Burkina Faso', 'slug' => 'BF', 'code' => 226, 'digit_length' => 8],
            ['name' => 'Burundi', 'slug' => 'BI', 'code' => 257, 'digit_length' => 10],
            ['name' => 'Cambodia', 'slug' => 'KH', 'code' => 855, 'digit_length' => 9],
            ['name' => 'Cameroon', 'slug' => 'CM', 'code' => 237, 'digit_length' => 9],
            ['name' => 'Canada', 'slug' => 'CA', 'code' => 1, 'digit_length' => 10],
            ['name' => 'Cape Verde', 'slug' => 'CV', 'code' => 238, 'digit_length' => 10],
            ['name' => 'Cayman Islands', 'slug' => 'KY', 'code' => 1345, 'digit_length' => 10],
            ['name' => 'Central African Republic', 'slug' => 'CF', 'code' => 236, 'digit_length' => 10],
            ['name' => 'Chad', 'slug' => 'TD', 'code' => 235, 'digit_length' => 8],
            ['name' => 'Chile', 'slug' => 'CL', 'code' => 56, 'digit_length' => 9],
            ['name' => 'China', 'slug' => 'CN', 'code' => 86, 'digit_length' => 11],
            ['name' => 'Christmas Island', 'slug' => 'CX', 'code' => 61, 'digit_length' => 10],
            ['name' => 'Cocos (Keeling) Islands', 'slug' => 'CC', 'code' => 672, 'digit_length' => 10],
            ['name' => 'Colombia', 'slug' => 'CO', 'code' => 57, 'digit_length' => 10],
            ['name' => 'Comoros', 'slug' => 'KM', 'code' => 269, 'digit_length' => 10],
            ['name' => 'Congo', 'slug' => 'CG', 'code' => 242, 'digit_length' => 10],
            ['name' => 'Congo, the Democratic Republic of the', 'slug' => 'CD', 'code' => 242, 'digit_length' => 10],
            ['name' => 'Cook Islands', 'slug' => 'CK', 'code' => 682, 'digit_length' => 5],
            ['name' => 'Costa Rica', 'slug' => 'CR', 'code' => 506, 'digit_length' => 8],
            ['name' => 'Côte d\'Ivoire', 'slug' => 'CI', 'code' => 225, 'digit_length' => 10],
            ['name' => 'Croatia', 'slug' => 'HR', 'code' => 385, 'digit_length' => 9],
            ['name' => 'Cuba', 'slug' => 'CU', 'code' => 53, 'digit_length' => 10],
            ['name' => 'Curaçao', 'slug' => 'CW', 'code' => 0, 'digit_length' => 10],
            ['name' => 'Cyprus', 'slug' => 'CY', 'code' => 357, 'digit_length' => 8],
            ['name' => 'Czech Republic', 'slug' => 'CZ', 'code' => 420, 'digit_length' => 9],
            ['name' => 'Denmark', 'slug' => 'DK', 'code' => 45, 'digit_length' => 8],
            ['name' => 'Djibouti', 'slug' => 'DJ', 'code' => 253, 'digit_length' => 10],
            ['name' => 'Dominica', 'slug' => 'DM', 'code' => 1767, 'digit_length' => 10],
            ['name' => 'Dominican Republic', 'slug' => 'DO', 'code' => 1809, 'digit_length' => 10],
            ['name' => 'Ecuador', 'slug' => 'EC', 'code' => 593, 'digit_length' => 9],
            ['name' => 'Egypt', 'slug' => 'EG', 'code' => 20, 'digit_length' => 10],
            ['name' => 'El Salvador', 'slug' => 'SV', 'code' => 503, 'digit_length' => 8],
            ['name' => 'Equatorial Guinea', 'slug' => 'GQ', 'code' => 240, 'digit_length' => 10],
            ['name' => 'Eritrea', 'slug' => 'ER', 'code' => 291, 'digit_length' => 10],
            ['name' => 'Estonia', 'slug' => 'EE', 'code' => 372, 'digit_length' => 10],
            ['name' => 'Ethiopia', 'slug' => 'ET', 'code' => 251, 'digit_length' => 10],
            ['name' => 'Falkland Islands (Malvinas)', 'slug' => 'FK', 'code' => 500, 'digit_length' => 5],
            ['name' => 'Faroe Islands', 'slug' => 'FO', 'code' => 298, 'digit_length' => 5],
            ['name' => 'Fiji', 'slug' => 'FJ', 'code' => 679, 'digit_length' => 10],
            ['name' => 'Finland', 'slug' => 'FI', 'code' => 358, 'digit_length' => 10],
            ['name' => 'France', 'slug' => 'FR', 'code' => 33, 'digit_length' => 10],
            ['name' => 'French Guiana', 'slug' => 'GF', 'code' => 594, 'digit_length' => 10],
            ['name' => 'French Polynesia', 'slug' => 'PF', 'code' => 689, 'digit_length' => 10],
            ['name' => 'French Southern Territories', 'slug' => 'TF', 'code' => 0, 'digit_length' => 10],
            ['name' => 'Gabon', 'slug' => 'GA', 'code' => 241, 'digit_length' => 10],
            ['name' => 'Gambia', 'slug' => 'GM', 'code' => 220, 'digit_length' => 10],
            ['name' => 'Georgia', 'slug' => 'GE', 'code' => 995, 'digit_length' => 10],
            ['name' => 'Germany', 'slug' => 'DE', 'code' => 49, 'digit_length' => 10],
            ['name' => 'Ghana', 'slug' => 'GH', 'code' => 233, 'digit_length' => 10],
            ['name' => 'Gibraltar', 'slug' => 'GI', 'code' => 350, 'digit_length' => 10],
            ['name' => 'Greece', 'slug' => 'GR', 'code' => 30, 'digit_length' => 10],
            ['name' => 'Greenland', 'slug' => 'GL', 'code' => 299, 'digit_length' => 10],
            ['name' => 'Grenada', 'slug' => 'GD', 'code' => 1473, 'digit_length' => 10],
            ['name' => 'Guadeloupe', 'slug' => 'GP', 'code' => 590, 'digit_length' => 10],
            ['name' => 'Guam', 'slug' => 'GU', 'code' => 1671, 'digit_length' => 10],
            ['name' => 'Guatemala', 'slug' => 'GT', 'code' => 502, 'digit_length' => 10],
            ['name' => 'Guernsey', 'slug' => 'GG', 'code' => NULL, 'digit_length' => 10],
            ['name' => 'Guinea', 'slug' => 'GN', 'code' => 224, 'digit_length' => 10],
            ['name' => 'Guinea-Bissau', 'slug' => 'GW', 'code' => 245, 'digit_length' => 10],
            ['name' => 'Guyana', 'slug' => 'GY', 'code' => 592, 'digit_length' => 10],
            ['name' => 'Haiti', 'slug' => 'HT', 'code' => 509, 'digit_length' => 10],
            ['name' => 'Heard Island and McDonald Mcdonald Islands', 'slug' => 'HM', 'code' => 0, 'digit_length' => 10],
            ['name' => 'Holy See (Vatican City State)', 'slug' => 'VA', 'code' => 39, 'digit_length' => 10],
            ['name' => 'Honduras', 'slug' => 'HN', 'code' => 504, 'digit_length' => 10],
            ['name' => 'Hong Kong', 'slug' => 'HK', 'code' => 852, 'digit_length' => 10],
            ['name' => 'Hungary', 'slug' => 'HU', 'code' => 36, 'digit_length' => 10],
            ['name' => 'Iceland', 'slug' => 'IS', 'code' => 354, 'digit_length' => 10],
            ['name' => 'India', 'slug' => 'IN', 'code' => 91, 'digit_length' => 10],
            ['name' => 'Indonesia', 'slug' => 'ID', 'code' => 62, 'digit_length' => 10],
            ['name' => 'Iran, Islamic Republic of', 'slug' => 'IR', 'code' => 98, 'digit_length' => 10],
            ['name' => 'Iraq', 'slug' => 'IQ', 'code' => 964, 'digit_length' => 10],
            ['name' => 'Ireland', 'slug' => 'IE', 'code' => 353, 'digit_length' => 10],
            ['name' => 'Isle of Man', 'slug' => 'IM', 'code' => NULL, 'digit_length' => 10],
            ['name' => 'Israel', 'slug' => 'IL', 'code' => 972, 'digit_length' => 10],
            ['name' => 'Italy', 'slug' => 'IT', 'code' => 39, 'digit_length' => 10],
            ['name' => 'Jamaica', 'slug' => 'JM', 'code' => 1876, 'digit_length' => 10],
            ['name' => 'Japan', 'slug' => 'JP', 'code' => 81, 'digit_length' => 10],
            ['name' => 'Jersey', 'slug' => 'JE', 'code' => NULL, 'digit_length' => 10],
            ['name' => 'Jordan', 'slug' => 'JO', 'code' => 962, 'digit_length' => 10],
            ['name' => 'Kazakhstan', 'slug' => 'KZ', 'code' => 7, 'digit_length' => 10],
            ['name' => 'Kenya', 'slug' => 'KE', 'code' => 254, 'digit_length' => 10],
            ['name' => 'Kiribati', 'slug' => 'KI', 'code' => 686, 'digit_length' => 10],
            ['name' => 'Korea, Democratic People\'s Republic of', 'slug' => 'KP', 'code' => 850, 'digit_length' => 10],
            ['name' => 'Korea, Republic of', 'slug' => 'KR', 'code' => 82, 'digit_length' => 10],
            ['name' => 'Kuwait', 'slug' => 'KW', 'code' => 965, 'digit_length' => 10],
            ['name' => 'Kyrgyzstan', 'slug' => 'KG', 'code' => 996, 'digit_length' => 10],
            ['name' => 'Lao People\'s Democratic Republic', 'slug' => 'LA', 'code' => 856, 'digit_length' => 10],
            ['name' => 'Latvia', 'slug' => 'LV', 'code' => 371, 'digit_length' => 10],
            ['name' => 'Lebanon', 'slug' => 'LB', 'code' => 961, 'digit_length' => 10],
            ['name' => 'Lesotho', 'slug' => 'LS', 'code' => 266, 'digit_length' => 10],
            ['name' => 'Liberia', 'slug' => 'LR', 'code' => 231, 'digit_length' => 10],
            ['name' => 'Libya', 'slug' => 'LY', 'code' => 218, 'digit_length' => 10],
            ['name' => 'Liechtenstein', 'slug' => 'LI', 'code' => 423, 'digit_length' => 10],
            ['name' => 'Lithuania', 'slug' => 'LT', 'code' => 370, 'digit_length' => 10],
            ['name' => 'Luxembourg', 'slug' => 'LU', 'code' => 352, 'digit_length' => 10],
            ['name' => 'Macao', 'slug' => 'MO', 'code' => 853, 'digit_length' => 10],
            ['name' => 'Macedonia, the Former Yugoslav Republic of', 'slug' => 'MK', 'code' => 389, 'digit_length' => 10],
            ['name' => 'Madagascar', 'slug' => 'MG', 'code' => 261, 'digit_length' => 10],
            ['name' => 'Malawi', 'slug' => 'MW', 'code' => 265, 'digit_length' => 10],
            ['name' => 'Malaysia', 'slug' => 'MY', 'code' => 60, 'digit_length' => 10],
            ['name' => 'Maldives', 'slug' => 'MV', 'code' => 960, 'digit_length' => 10],
            ['name' => 'Mali', 'slug' => 'ML', 'code' => 223, 'digit_length' => 10],
            ['name' => 'Malta', 'slug' => 'MT', 'code' => 356, 'digit_length' => 10],
            ['name' => 'Marshall Islands', 'slug' => 'MH', 'code' => 692, 'digit_length' => 10],
            ['name' => 'Martinique', 'slug' => 'MQ', 'code' => 596, 'digit_length' => 10],
            ['name' => 'Mauritania', 'slug' => 'MR', 'code' => 222, 'digit_length' => 10],
            ['name' => 'Mauritius', 'slug' => 'MU', 'code' => 230, 'digit_length' => 10],
            ['name' => 'Mayotte', 'slug' => 'YT', 'code' => 269, 'digit_length' => 10],
            ['name' => 'Mexico', 'slug' => 'MX', 'code' => 52, 'digit_length' => 10],
            ['name' => 'Micronesia, Federated States of', 'slug' => 'FM', 'code' => 691, 'digit_length' => 10],
            ['name' => 'Moldova, Republic of', 'slug' => 'MD', 'code' => 373, 'digit_length' => 10],
            ['name' => 'Monaco', 'slug' => 'MC', 'code' => 377, 'digit_length' => 10],
            ['name' => 'Mongolia', 'slug' => 'MN', 'code' => 976, 'digit_length' => 10],
            ['name' => 'Montenegro', 'slug' => 'ME', 'code' => NULL, 'digit_length' => 10],
            ['name' => 'Montserrat', 'slug' => 'MS', 'code' => 1664, 'digit_length' => 10],
            ['name' => 'Morocco', 'slug' => 'MA', 'code' => 212, 'digit_length' => 10],
            ['name' => 'Mozambique', 'slug' => 'MZ', 'code' => 258, 'digit_length' => 10],
            ['name' => 'Myanmar', 'slug' => 'MM', 'code' => 95, 'digit_length' => 10],
            ['name' => 'Namibia', 'slug' => 'NA', 'code' => 264, 'digit_length' => 10],
            ['name' => 'Nauru', 'slug' => 'NR', 'code' => 674, 'digit_length' => 10],
            ['name' => 'Nepal', 'slug' => 'NP', 'code' => 977, 'digit_length' => 10],
            ['name' => 'Netherlands', 'slug' => 'NL', 'code' => 31, 'digit_length' => 10],
            ['name' => 'New Caledonia', 'slug' => 'NC', 'code' => 687, 'digit_length' => 10],
            ['name' => 'New Zealand', 'slug' => 'NZ', 'code' => 64, 'digit_length' => 10],
            ['name' => 'Nicaragua', 'slug' => 'NI', 'code' => 505, 'digit_length' => 10],
            ['name' => 'Niger', 'slug' => 'NE', 'code' => 227, 'digit_length' => 10],
            ['name' => 'Nigeria', 'slug' => 'NG', 'code' => 234, 'digit_length' => 11],
            ['name' => 'Niue', 'slug' => 'NU', 'code' => 683, 'digit_length' => 10],
            ['name' => 'Norfolk Island', 'slug' => 'NF', 'code' => 672, 'digit_length' => 10],
            ['name' => 'Northern Mariana Islands', 'slug' => 'MP', 'code' => 1670, 'digit_length' => 10],
            ['name' => 'Norway', 'slug' => 'NO', 'code' => 47, 'digit_length' => 10],
            ['name' => 'Oman', 'slug' => 'OM', 'code' => 968, 'digit_length' => 10],
            ['name' => 'Pakistan', 'slug' => 'PK', 'code' => 92, 'digit_length' => 10],
            ['name' => 'Palau', 'slug' => 'PW', 'code' => 680, 'digit_length' => 10],
            ['name' => 'Palestine, State of', 'slug' => 'PS', 'code' => 970, 'digit_length' => 10],
            ['name' => 'Panama', 'slug' => 'PA', 'code' => 507, 'digit_length' => 10],
            ['name' => 'Papua New Guinea', 'slug' => 'PG', 'code' => 675, 'digit_length' => 10],
            ['name' => 'Paraguay', 'slug' => 'PY', 'code' => 595, 'digit_length' => 10],
            ['name' => 'Peru', 'slug' => 'PE', 'code' => 51, 'digit_length' => 10],
            ['name' => 'Philippines', 'slug' => 'PH', 'code' => 63, 'digit_length' => 10],
            ['name' => 'Pitcairn', 'slug' => 'PN', 'code' => 0, 'digit_length' => 10],
            ['name' => 'Poland', 'slug' => 'PL', 'code' => 48, 'digit_length' => 10],
            ['name' => 'Portugal', 'slug' => 'PT', 'code' => 351, 'digit_length' => 10],
            ['name' => 'Puerto Rico', 'slug' => 'PR', 'code' => 1787, 'digit_length' => 10],
            ['name' => 'Qatar', 'slug' => 'QA', 'code' => 974, 'digit_length' => 10],
            ['name' => 'Réunion', 'slug' => 'RE', 'code' => 262, 'digit_length' => 10],
            ['name' => 'Romania', 'slug' => 'RO', 'code' => 40, 'digit_length' => 10],
            ['name' => 'Russian Federation', 'slug' => 'RU', 'code' => 70, 'digit_length' => 10],
            ['name' => 'Rwanda', 'slug' => 'RW', 'code' => 250, 'digit_length' => 10],
            ['name' => 'Saint Barthélemy', 'slug' => 'BL', 'code' => 0, 'digit_length' => 10],
            ['name' => 'Saint Helena, Ascension and Tristan da Cunha', 'slug' => 'SH', 'code' => 290, 'digit_length' => 10],
            ['name' => 'Saint Kitts and Nevis', 'slug' => 'KN', 'code' => 1869, 'digit_length' => 10],
            ['name' => 'Saint Lucia', 'slug' => 'LC', 'code' => 1758, 'digit_length' => 10],
            ['name' => 'Saint Martin (French part)', 'slug' => 'MF', 'code' => 0, 'digit_length' => 10],
            ['name' => 'Saint Pierre and Miquelon', 'slug' => 'PM', 'code' => 508, 'digit_length' => 10],
            ['name' => 'Saint Vincent and the Grenadines', 'slug' => 'VC', 'code' => 1784, 'digit_length' => 10],
            ['name' => 'Samoa', 'slug' => 'WS', 'code' => 684, 'digit_length' => 10],
            ['name' => 'San Marino', 'slug' => 'SM', 'code' => 378, 'digit_length' => 10],
            ['name' => 'Sao Tome and Principe', 'slug' => 'ST', 'code' => 239, 'digit_length' => 10],
            ['name' => 'Saudi Arabia', 'slug' => 'SA', 'code' => 966, 'digit_length' => 10],
            ['name' => 'Senegal', 'slug' => 'SN', 'code' => 221, 'digit_length' => 10],
            ['name' => 'Serbia', 'slug' => 'RS', 'code' => 381, 'digit_length' => 10],
            ['name' => 'Seychelles', 'slug' => 'SC', 'code' => 248, 'digit_length' => 10],
            ['name' => 'Sierra Leone', 'slug' => 'SL', 'code' => 232, 'digit_length' => 10],
            ['name' => 'Singapore', 'slug' => 'SG', 'code' => 65, 'digit_length' => 10],
            ['name' => 'Sint Maarten (Dutch part)', 'slug' => 'SX', 'code' => 0, 'digit_length' => 10],
            ['name' => 'Slovakia', 'slug' => 'SK', 'code' => 421, 'digit_length' => 10],
            ['name' => 'Slovenia', 'slug' => 'SI', 'code' => 386, 'digit_length' => 10],
            ['name' => 'Solomon Islands', 'slug' => 'SB', 'code' => 677, 'digit_length' => 10],
            ['name' => 'Somalia', 'slug' => 'SO', 'code' => 252, 'digit_length' => 10],
            ['name' => 'South Africa', 'slug' => 'ZA', 'code' => 27, 'digit_length' => 10],
            ['name' => 'South Georgia and the South Sandwich Islands', 'slug' => 'GS', 'code' => 0, 'digit_length' => 10],
            ['name' => 'South Sudan', 'slug' => 'SS', 'code' => 0, 'digit_length' => 10],
            ['name' => 'Spain', 'slug' => 'ES', 'code' => 34, 'digit_length' => 10],
            ['name' => 'Sri Lanka', 'slug' => 'LK', 'code' => 94, 'digit_length' => 10],
            ['name' => 'Sudan', 'slug' => 'SD', 'code' => 249, 'digit_length' => 10],
            ['name' => 'Suriname', 'slug' => 'SR', 'code' => 597, 'digit_length' => 10],
            ['name' => 'Svalbard and Jan Mayen', 'slug' => 'SJ', 'code' => 47, 'digit_length' => 10],
            ['name' => 'Swaziland', 'slug' => 'SZ', 'code' => 268, 'digit_length' => 10],
            ['name' => 'Sweden', 'slug' => 'SE', 'code' => 46, 'digit_length' => 10],
            ['name' => 'Switzerland', 'slug' => 'CH', 'code' => 41, 'digit_length' => 10],
            ['name' => 'Syrian Arab Republic', 'slug' => 'SY', 'code' => 963, 'digit_length' => 10],
            ['name' => 'Taiwan', 'slug' => 'TW', 'code' => 886, 'digit_length' => 10],
            ['name' => 'Tajikistan', 'slug' => 'TJ', 'code' => 992, 'digit_length' => 10],
            ['name' => 'Tanzania, United Republic of', 'slug' => 'TZ', 'code' => 255, 'digit_length' => 10],
            ['name' => 'Thailand', 'slug' => 'TH', 'code' => 66, 'digit_length' => 10],
            ['name' => 'Timor-Leste', 'slug' => 'TL', 'code' => 670, 'digit_length' => 10],
            ['name' => 'Togo', 'slug' => 'TG', 'code' => 228, 'digit_length' => 10],
            ['name' => 'Tokelau', 'slug' => 'TK', 'code' => 690, 'digit_length' => 10],
            ['name' => 'Tonga', 'slug' => 'TO', 'code' => 676, 'digit_length' => 10],
            ['name' => 'Trinidad and Tobago', 'slug' => 'TT', 'code' => 1868, 'digit_length' => 10],
            ['name' => 'Tunisia', 'slug' => 'TN', 'code' => 216, 'digit_length' => 10],
            ['name' => 'Turkey', 'slug' => 'TR', 'code' => 90, 'digit_length' => 10],
            ['name' => 'Turkmenistan', 'slug' => 'TM', 'code' => 7370, 'digit_length' => 10],
            ['name' => 'Turks and Caicos Islands', 'slug' => 'TC', 'code' => 1649, 'digit_length' => 10],
            ['name' => 'Tuvalu', 'slug' => 'TV', 'code' => 688, 'digit_length' => 10],
            ['name' => 'Uganda', 'slug' => 'UG', 'code' => 256, 'digit_length' => 10],
            ['name' => 'Ukraine', 'slug' => 'UA', 'code' => 380, 'digit_length' => 10],
            ['name' => 'United Arab Emirates', 'slug' => 'AE', 'code' => 971, 'digit_length' => 10],
            ['name' => 'United Kingdom', 'slug' => 'GB', 'code' => 44, 'digit_length' => 10],
            ['name' => 'United States', 'slug' => 'US', 'code' => 1, 'digit_length' => 10],
            ['name' => 'United States Minor Outlying Islands', 'slug' => 'UM', 'code' => 1, 'digit_length' => 10],
            ['name' => 'Uruguay', 'slug' => 'UY', 'code' => 598, 'digit_length' => 10],
            ['name' => 'Uzbekistan', 'slug' => 'UZ', 'code' => 998, 'digit_length' => 10],
            ['name' => 'Vanuatu', 'slug' => 'VU', 'code' => 678, 'digit_length' => 10],
            ['name' => 'Venezuela, Bolivarian Republic of', 'slug' => 'VE', 'code' => 58, 'digit_length' => 10],
            ['name' => 'Viet Nam', 'slug' => 'VN', 'code' => 84, 'digit_length' => 10],
            ['name' => 'Virgin Islands, British', 'slug' => 'VG', 'code' => 1284, 'digit_length' => 10],
            ['name' => 'Virgin Islands, U.S.', 'slug' => 'VI', 'code' => 1340, 'digit_length' => 10],
            ['name' => 'Wallis and Futuna', 'slug' => 'WF', 'code' => 681, 'digit_length' => 10],
            ['name' => 'Western Sahara', 'slug' => 'EH', 'code' => 212, 'digit_length' => 10],
            ['name' => 'Yemen', 'slug' => 'YE', 'code' => 967, 'digit_length' => 10],
            ['name' => 'Zambia', 'slug' => 'ZM', 'code' => 260, 'digit_length' => 10],
            ['name' => 'Zimbabwe', 'slug' => 'ZW', 'code' => 263, 'digit_length' => 10],
        ];

        foreach ($countries as $key => $value) {
            Country::create($value);
        }
    }


}
