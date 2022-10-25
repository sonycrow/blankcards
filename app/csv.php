<?php
// Init
set_time_limit(0);
ini_set('error_reporting', E_ALL & ~E_NOTICE);

header("Content-type: text/plain");

// Campañas
$cpa =
    array(
        array(
            "campaign" => 1,
            "description" => "Campaña por defecto, solo en mercados activos, con valores de CPA mínimos establecidos por Trivago",
            "markets" => array(
                array('locale' => 'AR', 'cpa_value' => 5),
                array('locale' => 'AT', 'cpa_value' => 7),
                array('locale' => 'AU', 'cpa_value' => 5),
                array('locale' => 'CH', 'cpa_value' => 7),
                array('locale' => 'CO', 'cpa_value' => 7),
                array('locale' => 'CL', 'cpa_value' => 6),
                array('locale' => 'DE', 'cpa_value' => 7),
                array('locale' => 'DK', 'cpa_value' => 6),
                array('locale' => 'ES', 'cpa_value' => 7),
                array('locale' => 'FI', 'cpa_value' => 7),
                array('locale' => 'FR', 'cpa_value' => 5),
                array('locale' => 'HU', 'cpa_value' => 7),
                array('locale' => 'IE', 'cpa_value' => 6),
                array('locale' => 'IT', 'cpa_value' => 6),
                array('locale' => 'MX', 'cpa_value' => 6),
                array('locale' => 'NL', 'cpa_value' => 7),
                array('locale' => 'NO', 'cpa_value' => 7),
                array('locale' => 'PL', 'cpa_value' => 6),
                array('locale' => 'RO', 'cpa_value' => 7),
                array('locale' => 'SE', 'cpa_value' => 7),
                array('locale' => 'UK', 'cpa_value' => 6),
                array('locale' => 'US', 'cpa_value' => 7)
            )
        )
    );

// Listado de hoteles
$hotels = array(
    '3', '8', '11', '17', '18', '22', '45', '89', '95', '113', '263', '267', '273', '281', '287', '293', '299', '323',
    '325', '447', '455', '477', '485', '507', '535', '563', '567', '569', '581', '585', '591', '593', '633', '643',
    '645', '647', '693', '781', '807', '821', '822', '823', '824', '825', '831', '837', '849', '853', '861', '868',
    '895', '901', '902', '918', '919', '927', '928', '931', '932', '934', '935', '937', '939', '949', '967', '974',
    '975', '981', '990', '1009', '1010', '1140', '1141', '1142', '1148', '1150', '1151', '1153', '1174', '1175',
    '1180', '1181', '1182', '1183', '1184', '1185', '1193', '1197', '1212', '1222', '1226', '1229', '1231', '1240',
    '1241', '1253', '1259', '1260', '1272', '1273', '1278', '1279', '1284', '1293', '1294', '1295', '1296', '1326',
    '1334', '1350', '1351', '1359', '1361', '1371', '1372', '1376', '1383', '1384', '1387', '1389', '1390', '1392',
    '1393', '1398', '1399', '1400', '1401', '1403', '1404', '1406', '1409', '1425', '1453', '1461', '1465', '1466',
    '1472', '1478', '1481', '1495', '1497', '1498', '1535', '1544', '1545', '1546', '1558', '1574', '1576', '1588',
    '1589', '1609', '1610', '1612', '1626', '1635', '1645', '1647', '1650', '1651', '1652', '1657', '1659', '1662',
    '1673', '1690', '1694', '1702', '1705', '1709', '1716', '1723', '1736', '1751', '1753', '1754', '1755', '1756',
    '1758', '1762', '1764', '1769', '1771', '1773', '1774', '1776', '1791', '1795', '1796', '1804', '1805', '1808',
    '1814', '1815', '1817', '1818', '1820', '1823', '1829', '1830', '1831', '1832', '1838', '1849', '1850', '1858',
    '1866', '1870', '1877', '1880', '1883', '1896', '1904', '1906', '1930', '1935', '1944'
);

// Listado de hoteles piloto (comentar o eliminar si se quiere toda la lista)
$hotels = array('113', '1498', '1805', '1497');

// Transforma una colección en un CSV
function arrayToCsv(array $collection, string $separator): string
{
    $str = '';
    foreach ($collection as $line)
    {
        foreach (array_values($line) as $column) {
            $str .= $column . $separator;
        }

        $str  = substr($str, 0, -1);
        $str .= "\r\n";
    }

    return $str;
}

// Inicializamos CSV de campañas
$cpa_value_allocation = array(array('locale', 'campaign', 'cpa_value'));

// Generamos las campañas
foreach ($cpa as $campaign)
{
    foreach ($campaign['markets'] as $market) {
        $cpa_value_allocation[] = array($market['locale'], $campaign['campaign'], $market['cpa_value']);
    }
}

// Inicializamos CSV de campañas de hoteles
$campaign_allocation = array(array('partner_reference', 'locale', 'campaign'));

// Generamos los valores para cada hotel en cada mercado para cada campaña
foreach ($hotels as $hotel)
{
    foreach ($cpa as $campaign)
    {
        foreach ($campaign['markets'] as $market) {
            $campaign_allocation[] = array($hotel, $market['locale'], $campaign['campaign']);
        }
    }
}


echo "1_cpa_value_allocation_neobookings.csv\n---\n";
echo arrayToCsv($cpa_value_allocation, ",");

echo "\n\n\n";

echo "2_campaign_allocation_neobookings.csv\n---\n";
echo arrayToCsv($campaign_allocation, ",");