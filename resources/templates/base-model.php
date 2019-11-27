<?='<?php'?>


namespace <?=$namespace?>;

use <?=$extendsFullClassName?>;

/**
<?php foreach($fields as $key => $field) {
    echo " * @property {$field['type']} \${$field['name']}\n";
}?> */
class <?=$className?> extends <?=$extendsShortClassName?>
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = '<?=$table?>';

    /**
     * The "type" of the auto-incrementing ID.
     *
     * @var string
     */
    protected $keyType = 'integer';

    /**
     * @var array
     */
    protected $fillable = <?=$fieldsArr?>;

}
