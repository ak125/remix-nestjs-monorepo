<?php 
session_start();
// fichier de recuperation et d'affichage des parametres de la base de données
require_once('config/sql.conf.php');
// fichier de recuperation et d'affichage des parametres du site
require_once('config/meta.conf.php');
// get parameters
$quest = $_POST['MINE'];
$ask2page = $_POST['ASK2PAGE'];

if($ask2page==1)
{
		$query_mine = "SELECT DISTINCT TYPE_ID, TYPE_ALIAS, 
		MODELE_ID, MODELE_ALIAS, 
		MARQUE_ID, MARQUE_ALIAS
		FROM AUTO_TYPE_NUMBER_CODE
		JOIN AUTO_TYPE ON TYPE_ID = TNC_TYPE_ID
		JOIN AUTO_MODELE ON MODELE_ID = TYPE_MODELE_ID
		JOIN AUTO_MARQUE ON MARQUE_ID = MODELE_MARQUE_ID
		WHERE TNC_CNIT LIKE '$quest' AND TYPE_DISPLAY = 1";
		$request_mine = $conn->query($query_mine);
		if ($request_mine->num_rows > 0) 
		{
			$result_mine = $request_mine->fetch_assoc();
			$linktoCar = $domain."/".$Auto."/".$result_mine['MARQUE_ALIAS']."-".$result_mine['MARQUE_ID']."/".$result_mine['MODELE_ALIAS']."-".$result_mine['MODELE_ID']."/".$result_mine['TYPE_ALIAS']."-".$result_mine['TYPE_ID'].".html";
			?>
			<meta http-equiv="refresh" content="0; URL=<?php echo $linktoCar; ?>">
			<?php
		}
		else
		{
			?>
			<meta http-equiv="refresh" content="0; URL=<?php echo $domain; ?>/type-mine.html">
			<?php
		}	
}

if($ask2page==2)
{
		$pg_id = $_POST['PGMINE'];
		$query_mine_pg = "SELECT PG_ID, PG_ALIAS  
			FROM PIECES_GAMME 
			WHERE PG_ID = $pg_id";
		$request_mine_pg = $conn->query($query_mine_pg);
		if ($request_mine_pg->num_rows > 0) 
		{
			$result_mine_pg = $request_mine_pg->fetch_assoc();
			$query_mine = "SELECT DISTINCT TYPE_ID, TYPE_ALIAS, 
				MODELE_ID, MODELE_ALIAS, 
				MARQUE_ID, MARQUE_ALIAS
				FROM AUTO_TYPE_NUMBER_CODE
				JOIN AUTO_TYPE ON TYPE_ID = TNC_TYPE_ID
				JOIN AUTO_MODELE ON MODELE_ID = TYPE_MODELE_ID
				JOIN AUTO_MARQUE ON MARQUE_ID = MODELE_MARQUE_ID
				JOIN PIECES_RELATION_TYPE ON RTP_TYPE_ID = TYPE_ID
				JOIN PIECES ON PIECE_ID = RTP_PIECE_ID AND PIECE_PG_ID = RTP_PG_ID AND PIECE_PM_ID = RTP_PM_ID
				JOIN PIECES_GAMME ON PG_ID = PIECE_PG_ID
				WHERE TNC_CNIT LIKE '$quest' AND RTP_PG_ID = '$pg_id' AND TYPE_DISPLAY = 1 AND PIECE_DISPLAY = 1";
			$request_mine = $conn->query($query_mine);
			if ($request_mine->num_rows > 0) 
			{
				$result_mine = $request_mine->fetch_assoc();
				$linktoP = $domain."/".$Piece."/".$result_mine_pg['PG_ALIAS']."-".$result_mine_pg['PG_ID']."/".$result_mine['MARQUE_ALIAS']."-".$result_mine['MARQUE_ID']."/".$result_mine['MODELE_ALIAS']."-".$result_mine['MODELE_ID']."/".$result_mine['TYPE_ALIAS']."-".$result_mine['TYPE_ID'].".html";
				?>
				<meta http-equiv="refresh" content="0; URL=<?php echo $linktoP; ?>">
				<?php
			}
			else
			{
				?>
				<meta http-equiv="refresh" content="0; URL=<?php echo $domain; ?>/type-mine.html">
				<?php
			}
		}
		else
		{
			?>
			<meta http-equiv="refresh" content="0; URL=<?php echo $domain; ?>/type-mine.html">
			<?php
		}	
}
?>