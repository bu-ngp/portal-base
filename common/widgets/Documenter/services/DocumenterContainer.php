<?php
/**
 * Created by PhpStorm.
 * User: VOVANCHO
 * Date: 28.10.2017
 * Time: 11:37
 */

namespace common\widgets\Documenter\services;


use kartik\markdown\Markdown;
use yii\helpers\Url;

class DocumenterContainer
{
    private $_tabsContent = '';
    private $_pillsContent = '';
    private $_currentDocument = '';

    private $_tabsNames = [];
    private $_activeTab;


    /**
     * @param DocumenterViewer[][] $documents
     */
    public function __construct(array $documents)
    {
        foreach ($documents as $directory => $viewers) {
            foreach ($viewers as $key => $document) {
                if (!isset($this->_tabsNames[$document->getTabName()])) {
                    $this->_tabsNames[$document->getTabName()] = 'wk_' . hash('crc32', $document->getTabName());
                    if ($key === 0) {
                        $this->_activeTab = $document->getTabName();
                        $contentConverted = Markdown::convert($document->getContent());
                        $this->_currentDocument = <<<EOT
                    <div role="tabpanel" class="tab-pane active" id="{$this->_tabsNames[$document->getTabName()]}">$contentConverted</div> 
EOT;

                    }
                    $active = $this->_activeTab === $document->getTabName() ? ' class="active" ' : '';

                    $this->_tabsContent .= <<<EOT
                    <li role="presentation"$active><a href="#{$this->_tabsNames[$document->getTabName()]}" role="tab" data-toggle="tab">{$document->getTabName()}</a></li>
EOT;


                }

                if ($this->_activeTab === $document->getTabName()) {

                    $url = Url::current(['id' => 'niggas']);

                    $this->_pillsContent .= <<<EOT
                    <a class="list-group-item" href="$url">{$document->getPillName()}</a>
EOT;
                }


            }
        }
    }

    public function getPillsContent()
    {
        return $this->_pillsContent;
    }

    public function getTabsContent()
    {
        return $this->_tabsContent;
    }

    public function getCurrentDocument()
    {
        return $this->_currentDocument;
    }

}