<?php

// 人間クラス
class Human
{
    // フィールド
    protected $name;
    protected $clan;
    protected $amount_chakra;
    protected static $good_night = "Zzzz...";
    
    // コンストラクタ
    public function __construct(string $name, string $clan, int $amount_chakra)
    {
        $this->name = $name;
        $this->clan = $clan;
        $this->amount_chakra = $amount_chakra;
    }

    // 寝るメソッド
    public static function sleeping()
    {
        print(self::$good_night. "<br>");
    }

    // あいさつするメソッド
    public function greeting()
    {
        print("あいさつする：" . "私の名前は" . $this->name."です。". "一族は" . $this->clan . "です" . "<br>");
    }

    // チャクラを生成するメソッド
    public function generateChakra()
    {
        print("チャクラを練る：" . "私は忍者ではないのでチャクラを練ることができません" . "<br>");
    }

    // 分身を作成するメソッド
    public function executeShadowCloningTechnique()
    {
        print("分身の術!!：" . "術を発動するためのチャクラが足りません");
    }
}