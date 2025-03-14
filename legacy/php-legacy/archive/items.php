<?php
/*function get_items_list_array($pr_id, $mt_id)
{
    global $conn;
    $query = "SELECT su_id, su_name, pr_id, pr_name, pr_alias, pl_supplier_ref AS distinct_piece_ref, li_unique_part_id AS distinct_piece_id, at_name, pa_value, pl_price
        FROM part_links
        inner join part_list
        on li_unique_part_id = pl_unique_part_id
        INNER JOIN product
        ON pl_product_id = pr_id
        INNER JOIN part_supplier
        ON pl_supplier_id = su_id
        INNER JOIN part_attribute
        ON li_unique_part_id = pa_unique_part_id
        INNER JOIN attribute
        ON pa_attribute_id = at_id
        WHERE pl_product_id = $pr_id AND li_vehicle_id = $mt_id"; 
        //ORDER BY su_name";
    $request = $conn->query($query);
    if ($request->num_rows > 0) 
    {
        for ($set = array (); $result = $request->fetch_assoc(); $set[] = $result);
    }
    return $set;
}*/
function get_search_items_list_array($questCleaned)
{
    global $conn;
    $query = "SELECT DISTINCT PIECE_ID, PIECE_REF, PIECE_NAME, PIECE_NAME_COMP, PIECE_NAME_SIDE,
            PRS_REF, PIECE_HAS_IMG, PIECE_HAS_OEM, PIECE_QTY_SALE, PIECE_QTY_PACK, PIECE_WEIGHT_KGM,
            PG_ID, PG_NAME , PG_ALIAS ,
            PM_ID, PM_NAME, PM_LOGO, COALESCE(PIECE_DES,PM_QUALITY) AS PM_QUALITY, PM_OES, PM_NB_STARS, PRB_NAME
            , PRI_CONSIGNE_TTC, PRI_VENTE_HT, PRI_VENTE_TTC
            , CONCAT('rack/',PMI_FOLDER,'/',PMI_NAME,'.webp') AS PIECE_IMG ,
            PCL_CRI_ID, PCL_CRI_CRITERIA, PC_CRI_VALUE AS PCL_VALUE, PCL_CRI_UNIT
            FROM PIECES_REF_SEARCH
            INNER JOIN PIECES_REF_BRAND ON PRB_ID = PRS_PRB_ID
            INNER JOIN PIECES ON PIECE_ID = PRS_PIECE_ID AND PIECE_DISPLAY = 1
            INNER JOIN PIECES_GAMME ON PG_ID = PIECE_PG_ID AND PG_DISPLAY = 1
            INNER JOIN PIECES_PRICE ON PRI_PIECE_ID = PIECE_ID AND PRI_DISPO = 1
            INNER JOIN PIECES_MARQUE ON PM_ID = PRI_PM_ID AND PM_DISPLAY = 1
            LEFT JOIN PIECES_MEDIA_IMG ON PMI_PIECE_ID = PRI_PIECE_ID AND PMI_DISPLAY = 1
            LEFT JOIN PIECES_CRITERIA ON PC_PIECE_ID = PIECE_ID AND PC_DISPLAY = 1
            LEFT JOIN PIECES_CRITERIA_LINK ON PCL_CRI_ID = PC_CRI_ID AND PCL_PG_PID = PC_PG_PID AND PCL_DISPLAY = 1
            WHERE PRS_SEARCH = '$questCleaned'
            ORDER BY PIECE_PG_ID , PIECE_SORT, PRS_KIND , PRB_ID , (PIECE_QTY_SALE*PRI_VENTE_TTC), PIECE_ID , PCL_LEVEL, PCL_SORT"; 
    $request = $conn->query($query);
    if ($request->num_rows > 0) 
    {
        for ($set = array (); $result = $request->fetch_assoc(); $set[] = $result);
    }
    return $set;
}

/*function get_item_attribute($piece_id)
{
    global $conn;
    $query = "SELECT DISTINCT at_name, pa_value, pl_price
        FROM part_list
        INNER JOIN part_supplier
        ON pl_supplier_id = su_id
        INNER JOIN part_attribute
        ON pl_unique_part_id = pa_unique_part_id
        INNER JOIN attribute
        ON pa_attribute_id = at_id
        WHERE pl_unique_part_id = $piece_id";
    $request = $conn->query($query);
    return $request;
}
function display_item_by_id($piece_id)
{
    global $conn;
    $query = "SELECT DISTINCT su_id, su_name, pl_supplier_ref, pl_product_id, pl_unique_part_id, pl_price 
        FROM part_list  
        INNER JOIN part_supplier ON pl_supplier_id = su_id 
        WHERE pl_unique_part_id = $piece_id";
    $request = $conn->query($query);
    return $request;
}
function get_item_by_id($piece_id)
{
    global $conn;
    $query = "SELECT DISTINCT at_name, pa_value, pl_price
        FROM part_links
        inner join part_list
        on li_unique_part_id = pl_unique_part_id
        INNER JOIN part_supplier
        ON pl_supplier_id = su_id
        INNER JOIN part_attribute
        ON li_unique_part_id = pa_unique_part_id
        INNER JOIN attribute
        ON pa_attribute_id = at_id
        WHERE pl_product_id = $pr_id AND li_vehicle_id = $mt_id AND li_unique_part_id = $piece_id";
    $request = $conn->query($query);
    return $request;
}*/
?>