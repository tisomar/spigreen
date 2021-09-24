<?php



/**
 * Skeleton subclass for performing query and update operations on the 'qp1_google_shopping_categoria' table.
 *
 *
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package    propel.generator.qcommerce
 */
class CategoriaGoogleShoppingPeer extends BaseCategoriaGoogleShoppingPeer
{
    /*
     * Realiza a importacao de categoria com base no arquivo de categorias txt disponibilizado pelo Google
     * O arquivo vem no formato txt abaixo:
     * Categoria
     * Categoria > CategoriaFilha
     * Categoria > CategoriaFilha > CategoriaNeta
     * OutraCategoria
     * OutraCategoria > OutroFilho
     *
     * Observar que o metodo tem um limite de importacao de 1000 registros para evitar problemas de memoria
     */
    public static function importarCategorias($arquivoPath){
        Propel::disableInstancePooling();

        $paiTodos = CategoriaGoogleShoppingQuery::create()->findRoot();
        if(!($paiTodos instanceof CategoriaGoogleShopping)){
            $paiTodos = new CategoriaGoogleShopping();
            $paiTodos->setNome('CATEGORIAS');
            $paiTodos->makeRoot();
            $paiTodos->save();
        }

        $file = new SplFileObject($arquivoPath);

        $limitador = 1000;
        $ultimoPai = $paiTodos;
        while(!$file->eof()){
            if(--$limitador < 0){
                break;
            }

            $linha = $file->fgets();
            if(strpos($linha, ' > ') === false){
                $catePai = CategoriaGoogleShoppingQuery::create()->findOneByNome(trim($linha));
                if(!($catePai instanceof CategoriaGoogleShopping)){
                    $catePai = new CategoriaGoogleShopping();
                    $catePai->setNome(trim($linha));
                    $catePai->setDestaque(0);
                    if(!$paiTodos->hasChildren()){
                        $catePai->insertAsNextSiblingOf($paiTodos);
                    }else{
                        $catePai->insertAsFirstChildOf($paiTodos);
                    }
                    $catePai->save();
                }

                $ultimoPai = $catePai;
                continue;
            }

            $dividido = explode(' > ', $linha);

            foreach($dividido as $cateName){

                $cateNova = CategoriaGoogleShoppingQuery::create()->findOneByNome(trim($cateName));
                if(!($cateNova instanceof CategoriaGoogleShopping)){
                    $cateNova = new CategoriaGoogleShopping();
                    $cateNova->setNome(trim($cateName));
                    $cateNova->setDestaque(0);
                    if(!$ultimoPai->hasChildren()){
                        $cateNova->insertAsFirstChildOf($ultimoPai);
                    }else{
                        $cateNova->insertAsNextSiblingOf($ultimoPai);
                    }
                    $cateNova->save();
                }
                $ultimoPai = $cateNova;
            }
        }
    }
}
