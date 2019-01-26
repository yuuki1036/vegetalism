
<?=LOG_DISP_MODE && isset($log2) ? "<small>$log2</small><br>" : ''?>
<?=LOG_DISP_MODE && isset($log) ? "<small>$log</small>" : ''?>
</div>
<div class="mt-5">
    <p class="attention small alert alert-warning d-inline-block p-1 mb-0">こちらは学習のために制作したサイトです。<br>実際に売買は行われません。</p>

    <button type="button" class="btn btn-info btn-sm d-inline-block align-bottom ml-2" data-toggle="modal" data-target="#exampleModal">このサイトについて</button>

    <div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">このサイトについて</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                    <div class="modal-body">
                        <p class="mb-0">閲覧いただきありがとうございます。こちらは藤岡勇樹が勉強・就活のために作った模擬ECサイトです。サイトの脆弱性対策やデータベースの操作について学びました。<br><br>
                        &lt;仕様&gt;<br>
                        使用言語：PHP, JavaScript, HTML/CSS<br>
                        フレームワーク：Bootstrap<br>
                        環境：Apache<br>
                        データベース：MySQL<br>
                        バージョン管理：GitHub<br>
                        <br>
                        &lt;脆弱性対策&gt;<br>
                        入力値の処理<br>
                        HTMLのエスケープ処理(XSS対策)<br>
                        静的プレースホルダの使用(SQLインジェクション対策)<br>
                        管理画面にDigest認証<br>
                        <br>
                        &lt;特徴&gt;<br>
                        条件を指定して商品検索<br>
                        季節に応じた旬の野菜を表示<br>
                        毎週安売り商品が入れ替わる<br>
                        料理から商品を検索<br>
                        注文確認メールの送信<br>
                        商品・料理を管理画面から編集できる<br>
                        <br>
                        <br>
                        ソースコードは<a href="https://github.com/yuuki1036/vegetalism" target="_blank">こちら</a>です。
                        </p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">閉じる</button>
                    </div>
            </div>
        </div>
    </div>
</div>

</div>

<!-- Bootstrap and jQuery -->
<script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.6/umd/popper.min.js" integrity="sha384-wHAiFfRlMFy6i5SRaxvfOCifBUQy1xHdJ/yoi7FRNXMRBu5WHdZYu1hA6ZOblgut" crossorigin="anonymous"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.2.1/js/bootstrap.min.js" integrity="sha384-B0UglyR+jN6CkvvICOB2joaf5I4l3gm9GU6Hc1og6Ls7i6U/mkkaduKaBhlAXv9k" crossorigin="anonymous"></script>
<!-- cookie.js -->
<script src="<?=h(ROOT_NAME)?>/js/jquery.cookie.js"></script>
<!-- owl carousel -->
<script src="<?=h(ROOT_NAME)?>/js/owl.carousel.min.js"></script>
<!-- common js -->
<script src="<?=h(ROOT_NAME)?>/js/common.js"></script>
</body>
</html>
