<?php
function get_gamme_privileg($pg_id)
{
    global $conn;
    $query = "SELECT PG_DISPLAY 
    FROM PIECES_GAMME 
    WHERE PG_ID = $pg_id  AND PG_LEVEL IN (1,2)";
    $request = $conn->query($query);
    $result = $request->fetch_assoc();
    return $result;
}
function get_gamme_by_id($pg_id)
{
    global $conn;
    $query = "SELECT PG_ALIAS, PG_NAME, PG_NAME_META, PG_RELFOLLOW, PG_IMG, PG_WALL, MF_ID, MF_NAME, MF_NAME_META 
    FROM PIECES_GAMME 
    JOIN CATALOG_GAMME ON MC_PG_ID = PG_ID
    JOIN CATALOG_FAMILY ON MF_ID = MC_MF_PRIME
    WHERE PG_ID = $pg_id 
    AND PG_DISPLAY = 1 AND PG_LEVEL IN (1,2) 
    AND MF_DISPLAY = 1";
    $request = $conn->query($query);
    $result = $request->fetch_assoc();
    return $result;
}
function get_gamme_list()
{
    global $conn;
    $query = "SELECT PG_ALIAS, PG_NAME, PG_NAME_META, PG_RELFOLLOW, PG_IMG, PG_WALL, MF_ID, MF_NAME, MF_NAME_META 
    FROM PIECES_GAMME 
    JOIN CATALOG_GAMME ON MC_PG_ID = PG_ID
    JOIN CATALOG_FAMILY ON MF_ID = MC_MF_PRIME
    WHERE PG_DISPLAY = 1 AND PG_LEVEL IN (1,2) 
    AND MF_DISPLAY = 1 LIMIT 1";
    $request = $conn->query($query);
    return $request;
}

/*

TABLEAU 

function get_gamme_list()
{
    global $conn;
    $query = "SELECT PG_ALIAS, PG_NAME, PG_NAME_META, PG_RELFOLLOW, PG_IMG, PG_WALL, MF_ID, MF_NAME, MF_NAME_META 
    FROM PIECES_GAMME 
    JOIN CATALOG_GAMME ON MC_PG_ID = PG_ID
    JOIN CATALOG_FAMILY ON MF_ID = MC_MF_PRIME
    WHERE PG_DISPLAY = 1 AND PG_LEVEL IN (1,2) 
    AND MF_DISPLAY = 1 LIMIT 1";
    $request = $conn->query($query);
    $result=array();
    $nb = $request->num_rows;
    if($nb>1)
    {
        while($row = $request->fetch_assoc())
            $result[] = $row;
    }
    else
    {
        $result = $request->fetch_assoc();
    }
    return $result;
}*/
?>
<?php
/*
require_once('config/v7.model.pg.php');
$query_gamme_list = get_gamme_list();
if (is_array($query_gamme_list))
{
    foreach ($query_gamme_list as $row) 
    {
    echo $row['MF_NAME']."<br>";
    }
}
else
{
    echo $query_gamme_list['PG_NAME']." - ".$query_gamme_list['PG_ALIAS']."<br>";
}*/
?>