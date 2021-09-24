<?php

class QPropelPager extends PropelPager
{

    public function __construct($c = null, $peerClass = null, $peerSelectMethod = null, $page = 1, $rowsPerPage = 20)
    {
        parent::__construct($c, $peerClass, $peerSelectMethod, $page, $rowsPerPage);

        if (!empty($_SERVER['REQUEST_URI'])) {
            $_SESSION['ULTIMA_LISTAGEM'] = $_SERVER['REQUEST_URI'];
        }
    }

    /**
     *
     * Retorna HTML dos links de paginação para funcionar com os filtros padrao da area admin
     *
     * @return string HTML dos links para paginação
     * @throws Exception
     */
    public function showPaginacao()
    {
        return \QPress\Template\Widget::render('admin/propel.pager', array('pager' => $this), true);
    }

    /**
     *
     * Retorna HTML dos links de paginação para funcionar com os filtros padrao do site
     *
     * @return string HTML dos links para paginação
     */
    public function showPaginacaoSite($url)
    {

        // CONFIGURA O ROOT PATH
        if (dirname($_SERVER["PHP_SELF"]) == DIRECTORY_SEPARATOR) {
            $root_path = "";
        } else {
            $root_path = dirname($_SERVER["PHP_SELF"]);
        }

        $root_path = str_replace("/controlador", "", $root_path);


        $ret = '';

        if ($this->getTotalPages() > 1) {
            $ret = '<div class="paginacao">
                    <div class="pag-cont">
                    <div class="pag-esq"></div>
                    <div class="pag-meio">
                    <ul>';

            if ($link = $this->getPrev()) {
                $ret .= '<li>
                  <a href="' . $url . '/' . $link . '" ><img src="' . $root_path . '/images/commons/ico-pag-esq.gif" alt="Voltar" /></a>
               </li>';
            }

            foreach ($this->getPrevLinks() as $link) {
                $ret .= '<li class="off">
                  <a href="' . $url . '/' . $link . '" >' . $link . '</a>
               </li>';
            }

            $ret .= '<li class="on" >
                  <a href="' . $url . '/' . $link . '" >' . $this->getPage() . '</a>
               </li>';

            foreach ($this->getNextLinks() as $link) {
                $ret .= '<li class="off">
                  <a href="' . $url . '/' . $link . '" >' . $link . '</a>
               </li>';
            }

            if ($link = $this->getNext()) {
                $ret .= '<li>
                  <a href="' . $url . '/' . $link . '" ><img src="' . $root_path . '/images/commons/ico-pag-dir.gif" alt="Avan&ccedil;ar"  /></a>
               </li>';
            }

            $ret .= ' </ul>
                      </div>
                      <div class="pag-dir"></div>
                      </div>
                      </div>';
        }

        return $ret;
    }
}
