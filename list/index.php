<?php
    $listId = $_GET['id'] ?? -1;
    $PageTitle = "List";
    require "../base.php";
    require '../header.php';

    $stmt = $conn->prepare("SELECT * FROM `lists` WHERE `ListID` = ?;");
    $stmt->bind_param("i", $listId);
    $stmt->execute();
    $list = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    $title = htmlspecialchars($list['Title']);

    if (is_null($list))
        die("List not found");
?>

<style>
    .container {
        background-color: darkslategray;
        width: 100%;
        box-sizing: border-box;
        padding: 1em;
    }

    .container h1 {
        margin-bottom: 0em;
    }

    .list-container {
        display: flex;
        flex-direction: column;
        width: 100%;
        box-sizing: border-box;
    }

    .list-item {
        box-sizing: border-box;
        min-height: 12em;
        display:flex;
        align-items: center;
        padding: 1em;
    }

    .list-item>div {
        margin-left:1em;
    }
</style>

<div class="container">
    <h1><?php echo $title; ?></h1>
    <span class="subText">
        Made by <a href="../profile/<?php echo $list["UserID"]; ?>"><?php echo GetUserNameFromId($list["UserID"], $conn); ?></a><br>
    </span>
    <hr>
    <div>
        <?php echo ParseCommentLinks($conn, nl2br(htmlspecialchars($list['Description'], ENT_COMPAT, "ISO-8859-1"))); ?>
    </div>
</div>

<?php if ($loggedIn && $userId == $list["UserID"]) { ?>
    <div style="margin-top: 1em;">
            <a href="edit/?id=<?php echo $listId; ?>"><span class="subText"><i class="icon-edit"></i> Edit</span></a>
    </div>
<?php } ?>
<hr>

<div class="list-container">
    <?php
    $stmt = $conn->prepare("SELECT * FROM list_items WHERE ListID = ?;");
    $stmt->bind_param("i", $listId);
    $stmt->execute();
    $result = $stmt->get_result();
    $stmt->close();

    while ($listItem = $result->fetch_assoc()){
        list($imageUrl, $title) = getListItemDisplayInformation($listItem, $conn);
        ?>
        <div class="list-item alternating-bg">
            <div>
                #<?php echo $listItem['order']; ?>
            </div>
            <div style="text-align: center; width: 8em;">
                <a href="/mapset/"><img src="<?php echo $imageUrl; ?>" class="diffThumb" style="height: 8em; width: 8em;" onerror="this.onerror=null; this.src='../../../charts/INF.png';" /></a><br>
                <span class="subText"><?php echo $title; ?></span>
            </div>
            <div style="flex-grow: 1; box-sizing: border-box;">
                <?php echo ParseCommentLinks($conn, nl2br(htmlspecialchars($listItem['Description'], ENT_COMPAT, "ISO-8859-1"))); ?>
            </div>
        </div>
        <?php
    }
    ?>
</div>

<?php
    require '../footer.php';
?>