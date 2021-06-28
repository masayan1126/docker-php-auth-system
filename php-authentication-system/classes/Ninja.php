<?php

// 人間クラスを継承した忍者クラス
class Ninja extends Human
{
    // チャクラを生成するメソッド(Humanクラスをオーバーライド)
    public function generateChakra()
    {
        $this->amount_chakra = 100;
        print("チャクラを練る：" ."チャクラ量が" .$this->amount_chakra . "になりました");
    }

    // 分身を作成するメソッド(Humanクラスをオーバーライド)
    public function executeShadowCloningTechnique()
    {
        $this->amount_chakra = $this->amount_chakra  - 50;
        print("<br>". "分身の術!!：" . "分身を1体作りました。残りチャクラは" . $this->amount_chakra . "です" );
    }
}