<?php 
$dsn = 'mysql:dbname=vn6nc6cs8uz15dnh;host=klbcedmmqp7w17ik.cbetxkdyhwsb.us-east-1.rds.amazonaws.com;charset=utf8mb4';
$user = 'ipdlhzhetgmr3pk2';
$password = 'anst1nhje7564cbn';

try {
    //インスタンス化
    $pdo = new PDO($dsn, $user, $password);

    if(isset($_GET['order'])){
        //orderの値があれば変数$orderに値を代入する
        $order = $_GET['order'];
    } else {
        $order = NULL;
    }

    if(isset($_GET['keyword'])){
        $keyword = $_GET['keyword'];
    } else {
        $keyword = NULL;
    }

    if ($order === 'desc'){
        //$orderの中身がdescであれば$sqlに降順のSQL文を代入
        $sql_select = 'SELECT * FROM products WHERE product_name LIKE :keyword ORDER BY updated_at DESC';
    } else {
        //$orderの中身がdesc以外であれば$sqlに昇順のSQL文を代入
        $sql_select = 'SELECT * FROM products WHERE product_name LIKE :keyword ORDER BY updated_at ASC';
    } 

    //SQL文を用意する (動的であるのでprepare)
    $stmt_select = $pdo->prepare($sql_select);

    //SQLのLIKE句で使うため、変数$keyword(検索ワード)の前後を%で囲む(部分一致)
    //補足: particle_match=部分一致
    $particle_match = "%{$keyword}%";

    //bindValue()メソッドを使って実際の値をプレースホルダ―にバインドする(割り当てる)
    $stmt_select->bindValue(':keyword', $particle_match, PDO::PARAM_STR);

    //SQL文を実行する
    $stmt_select->execute();

    //SQL文の実行結果を配列で取得する
    $products = $stmt_select->fetchAll(PDO::FETCH_ASSOC);
} catch(PDOException $e){
    exit($e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="ja">
    <head>
        <meta charset='UTF-8'>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>商品一覧</title>
        <link rel="stylesheet" href="css/style.css">

        <!-- Google fontsの読み込み -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+JP&display=swap" rel="stylesheet">
    </head>
    <body>
        <header>
            <nav>
                <a href="index.php">商品管理アプリ</a>
            </nav>
        </header>
        <main>
            <article class="products">
                <h1>商品一覧</h1>
                <?php 
                //(商品の登録・編集・削除後)messageバラメータの値を受け取っていれば、それを表示する
                if(isset($_GET['message'])){
                    echo "<p class='success'>{$_GET['message']}</p>";
                }
                ?>
                <div class="products-ui">
                    <div>
                        <!--画像をクリックするとorderとkeywordの値の両方をわたす-->
                        <a href="read.php?order=desc&keyword=<?= $keyword ?>">
                            <img src="images/desc.png" alt="降順に並び替え" class="sort_img">
                        </a>
                        <!--画像をクリックするとorderとkeywordの値の両方をわたす-->
                        <a href="read.php?order=asc&keyword=<?= $keyword ?>">
                            <img src="images/asc.png" alt="昇順に並び替え" class="sort_img">
                        </a> 
                        <form action="read.php" method="get" class="search-form">
                            <input type="hidden" name="order" value="<?= $order ?>">
                            <input type="text" class="search-box" placeholder="商品名で検索" name="keyword" value="<?= $keyword ?>">
                        </form>    
                    </div>
                    <a href="create.php" class="btn">商品登録</a>
                </div>
                <table class="products-table">
                    <tr>
                        <th>商品コード</th>
                        <th>商品名</th>
                        <th>単価</th>
                        <th>在庫数</th>
                        <th>仕入先コード</th>
                        <th>編集</th>
                        <th>削除<th>
                    </tr>
                    <?php 
                    //配列の中身を順番に取り出し、表形式で出力する
                    foreach($products as $product){
                        $table_row = "
                        <tr>
                        <td>{$product['product_code']}</td>
                        <td>{$product['product_name']}</td>
                        <td>{$product['price']}</td>
                        <td>{$product['stock_quantity']}</td>
                        <td>{$product['vendor_code']}</td>
                        <td><a href='update.php?id={$product['id']}'><img src='images/edit.png' alt='編集' class='edit-icon'></a></td>
                        <td><a href='delete.php?id={$product['id']}'><img src='images/delete.png' alt='削除' class='delete-icon'></a></td>
                        </tr>
                        ";
                        echo $table_row;
                    } 
                    ?>
                </table>
            </article>
        </main>
        <footer>
            <p class="copyright">&copy; 商品管理アプリ ALL rights reserved.</p>
        </footer>
    </body>
</html>