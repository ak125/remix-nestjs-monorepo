<?php
//parametrage des liens clefs dansle site web
$domain='https://www.automecanik.com/massdoc';
$domainparent='https://www.automecanik.com';
$domainStaticFiles='https://4714711e034622730849-4d36b3a7f81d82c66bad0a46c55c8159.ssl.cf3.rackcdn.com';
//parametrage des données relatifs au site
$domainwebsitename='Automecanik.com';
$domainwebsitenameGroup="MASSDOC";
$domainidentifierNbr=1;
$domainidentifierLettre="A";
$companyidentifier=1;
$domainwebsitecontact='contact@automecanik.com';
$domainwebsitetel='01 77 69 57 14';
$domainwebsitetel2='01 77 69 57 79';
$domainname='Massdoc Reseller Plateform';
$domaincorename='Massdoc Reseller Plateform';
// parametrage de la langue dans le site
$hr='fr';
$HR='FR';
$hrHR='fr-FR';
$hrQuery=1;

$Currency = "€";
// nomination pour l'url rewriting

// gestion d'access
$accessPermittedLink = $domain."/welcome";
$accessRefusedLink = $domain."/denied";
$accessExpiredLink = $domain."/expired";
$accessSuspendedLink = $domain."/suspended";
$destinationLinkWelcome = $domain."/welcome";

// alias departement et rubriques
$aliasSeoType = "motorisation";
$aliasSeoGamme = "gamme";
$aliasSeoGammeType = "p";
$aliasSeoWebpConvertor = "webpconvertor";
$aliasSeoRobot = "robot";
$aliasSeoSitemap = "sitemap";
$aliasSeoCrossGamme = "crossgamme";
$aliasSeoCrossGammeCar = "crossgammecar";
$aliasParamsIp = "ip";

?>

<?php
function url_title($string, $separator = '-')
{
        $_transliteration = array(
            '/ä|æ|ǽ/' => 'ae',
            '/ö|œ/' => 'oe',
            '/ü/' => 'ue',
            '/Ä/' => 'Ae',
            '/Ü/' => 'Ue',
            '/Ö/' => 'Oe',
            '/À|Á|Â|Ã|Å|Ǻ|Ā|Ă|Ą|Ǎ/' => 'A',
            '/à|á|â|ã|å|ǻ|ā|ă|ą|ǎ|ª/' => 'a',
            '/Ç|Ć|Ĉ|Ċ|Č/' => 'C',
            '/ç|ć|ĉ|ċ|č/' => 'c',
            '/Ð|Ď|Đ/' => 'D',
            '/ð|ď|đ/' => 'd',
            '/È|É|Ê|Ë|Ē|Ĕ|Ė|Ę|Ě/' => 'E',
            '/è|é|ê|ë|ē|ĕ|ė|ę|ě/' => 'e',
            '/Ĝ|Ğ|Ġ|Ģ/' => 'G',
            '/ĝ|ğ|ġ|ģ/' => 'g',
            '/Ĥ|Ħ/' => 'H',
            '/ĥ|ħ/' => 'h',
            '/Ì|Í|Î|Ï|Ĩ|Ī|Ĭ|Ǐ|Į|İ/' => 'I',
            '/ì|í|î|ï|ĩ|ī|ĭ|ǐ|į|ı/' => 'i',
            '/Ĵ/' => 'J',
            '/ĵ/' => 'j',
            '/Ķ/' => 'K',
            '/ķ/' => 'k',
            '/Ĺ|Ļ|Ľ|Ŀ|Ł/' => 'L',
            '/ĺ|ļ|ľ|ŀ|ł/' => 'l',
            '/Ñ|Ń|Ņ|Ň/' => 'N',
            '/ñ|ń|ņ|ň|ŉ/' => 'n',
            '/Ò|Ó|Ô|Õ|Ō|Ŏ|Ǒ|Ő|Ơ|Ø|Ǿ/' => 'O',
            '/ò|ó|ô|õ|ō|ŏ|ǒ|ő|ơ|ø|ǿ|º/' => 'o',
            '/Ŕ|Ŗ|Ř/' => 'R',
            '/ŕ|ŗ|ř/' => 'r',
            '/Ś|Ŝ|Ş|Ș|Š/' => 'S',
            '/ś|ŝ|ş|ș|š|ſ/' => 's',
            '/Ţ|Ț|Ť|Ŧ/' => 'T',
            '/ţ|ț|ť|ŧ/' => 't',
            '/Ù|Ú|Û|Ũ|Ū|Ŭ|Ů|Ű|Ų|Ư|Ǔ|Ǖ|Ǘ|Ǚ|Ǜ/' => 'U',
            '/ù|ú|û|ũ|ū|ŭ|ů|ű|ų|ư|ǔ|ǖ|ǘ|ǚ|ǜ/' => 'u',
            '/Ý|Ÿ|Ŷ/' => 'Y',
            '/ý|ÿ|ŷ/' => 'y',
            '/Ŵ/' => 'W',
            '/ŵ/' => 'w',
            '/Ź|Ż|Ž/' => 'Z',
            '/ź|ż|ž/' => 'z',
            '/Æ|Ǽ/' => 'AE',
            '/ß/' => 'ss',
            '/Ĳ/' => 'IJ',
            '/ĳ/' => 'ij',
            '/Œ/' => 'OE',
            '/ƒ/' => 'f',
            '/ au /' => '-',
            '/ des /' => '-',
            '/ de /' => '-',
            '/ du /' => '-',
            '/ d\'/' => '-',
            '/ le /' => '-',
            '/ la /' => '-',
            '/ les /' => '-',
            '/ l\'/' => '-',
            '/ et /' => '-',
            '/ & /' => '-'
        );
        $quotedReplacement = preg_quote($separator, '/');
        $merge = array(
            '/[^\s\p{Zs}\p{Ll}\p{Lm}\p{Lo}\p{Lt}\p{Lu}\p{Nd}]/mu' => ' ',
            '/[\s\p{Zs}]+/mu' => $separator,
            sprintf('/^[%s]+|[%s]+$/', $quotedReplacement, $quotedReplacement) => '',
        );
        $map = $_transliteration + $merge;
        unset($_transliteration);
        return strtolower(trim(preg_replace(array_keys($map), array_values($map), $string)));
}

function getDepartmentAlias()
{
    $uri_path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    $uri_segments = explode('/', $uri_path);
    return $uri_segments[2];
}
function getsecondDepartmentAlias()
{
    $uri_path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    $uri_segments = explode('/', $uri_path);
    return $uri_segments[3];
}
function getthirdDepartmentAlias()
{
    $uri_path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    $uri_segments = explode('/', $uri_path);
    return $uri_segments[4];
}
function getfourthDepartmentAlias()
{
    $uri_path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    $uri_segments = explode('/', $uri_path);
    return $uri_segments[5];
}
function getpiecerefSearch()
{
    $uri_path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    $uri_segments = explode('/', $uri_path);
    return $uri_segments[4];
}
?>

