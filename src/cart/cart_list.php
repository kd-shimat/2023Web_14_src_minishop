<?php
require_once  __DIR__  .  '/../header.php';	// header.phpを読み込む
?>

<h3>カートの商品</h3>
<?php
// カート内のすべてのデータを取り出す		
$cartItems = $cart->getItems();
if (empty($cartItems)) {
	echo '<h4>お客様のショッピングカートに商品はありません。</h4>';
	echo '<a href="../index.php">ジャンル選択に戻る</a>';
} else {
?>
	<table>
		<tr>
			<th>&nbsp;</th>
			<th>商品名</th>
			<th>メーカー・著者<br>アーティスト</th>
			<th>価格</th>
			<th>注文数</th>
			<th>金額</th>
			<th>削除</th>
		</tr>
		<?php
		// カート内のすべてのデータを取り出す													
		// $cartItems = $cart->getItems();
		$total = 0;
		foreach ($cartItems as $item) {
			echo '<tr><td class="td_mini_img"><img class="mini_img" 													
			src="../images/' . $item['image'] . '"></td>';
			echo '<td class="td_item_name">' . $item['name'] . '</td>';
			echo '<td class="td_item_maker">' . $item['maker'] . '</td>';
			echo '<td class="td_right">&yen;' . number_format($item['price']) . '</td>';
			echo '<form method="POST" action="cart_change.php">';															// もともとあったコードは不要		
			echo '<td><select name="quantity">';
			for ($i = 1; $i <= 10; $i++) {
				echo '<option value="' . $i . '"';
				if ($i == $item['quantity']) {
					echo ' selected>';
				} else {
					echo  '>';
				}
				echo $i . '</option>';
			}
			echo '</select>';
			echo '<input type="hidden" name="ident" value="' . $item['ident'] . '">';
			echo '&nbsp;<input type="submit" value="変更"></td>';
			echo '</form>';
			echo '<td class="td_right">&yen;' . number_format($item['price'] * $item['quantity']) . '</td>';
			echo '<form method="POST" action="cart_delete.php">';
			echo '<td><input type="hidden" name="ident" value="' . $item['ident'] . '">';
			echo '<input type="submit" value="削除"></td>';
			echo '</form>';
			echo '</tr>';
			$total += $item['price'] * $item['quantity'];
		}
		?>
		<tr>
			<th colspan="5">合計金額</th>
			<td class="td_right">&yen;<?php echo number_format($total); ?></td>
		</tr>
	</table>
	<br>
	<a href="../index.php">ジャンル選択に戻る</a>&nbsp;&nbsp;<a href="../order/order_now.php">注文する</a>

<?php
}	// 15行目のif文の最後の「 } 」(波括弧閉じる)
require_once  __DIR__ . '/../footer.php';  										// footer.phpを読み込む	
?>