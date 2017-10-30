<?php
/**
 * Created by PhpStorm.
 * User: VOVANCHO
 * Date: 28.10.2017
 * Time: 11:37
 */

namespace common\widgets\Documenter\services;


use kartik\markdown\Markdown;
use Yii;
use yii\helpers\Url;
use yii\web\Response;
use yii\web\View;

class DocumenterContainer
{
    private $_documents;

    private $_tabsLinks = '';
    private $_tabsContent = '';
    private $_pillsContent = '';
    private $_currentDocumentContent = '';

    private $_tabsNames = [];

    /**
     * @param DocumenterViewer[][] $documents
     */
    public function __construct(array $documents)
    {
        $this->_documents = $documents;
        $this->initTabs();

        $this->_tabsLinks = implode('', array_map(function ($tabs) {
            return $tabs['tabLink'];
        }, $this->_tabsNames));

        $this->_tabsContent = implode('', array_map(function ($tabs) {
            return $tabs['tabContent'];
        }, $this->_tabsNames));

        $this->_pillsContent = implode('', array_map(function ($tabs) {
            ksort($tabs['pills']);
            return implode('', array_reverse($tabs['pills']));
        }, $this->_tabsNames));
    }

    public function getPillsContent()
    {
        return $this->_pillsContent;
    }

    public function getTabsLinks()
    {
        return $this->_tabsLinks;
    }

    public function getTabsContent()
    {
        return $this->_tabsContent;
    }

    public function getCurrentDocumentContent()
    {
        return $this->_currentDocumentContent;
    }

    protected function initTabs()
    {
        /** @var DocumenterViewer[][] $viewers */
        foreach ($this->_documents as $directory => $viewers) {
            foreach ($viewers as $key => $document) {
                $tabHash = 't_' . hash('crc32', $document->getTabName());
                $pillHash = 'p_' . hash('crc32', $document->getPillName());

                if ($document->isAllowed()) {
                    if (!isset($this->_tabsNames[$document->getTabName()])) {
                        $active = '';
                        $pillShow = ' wkdoc-pill-hide';
                        $activeTabContent = '';
                        if ($key === 0) {
                            $active = ' class="active" ';
                            $pillShow = ' wkdoc-pill-show';
                            $activeTabContent = ' active';
                        }

                        $this->_tabsNames[$document->getTabName()] = [
                            'tabLink' => "<li role=\"presentation\"$active><a class=\"wkdoc-tab-link\" href=\"#$tabHash\" role=\"tab\" data-toggle=\"tab\">{$document->getTabName()}</a></li>"
                        ];

                        $contentConverted = Markdown::convert($document->getContent());
                        $this->_tabsNames[$document->getTabName()]['tabContent'] = "<div role=\"tabpanel\" class=\"tab-pane fade in$activeTabContent\" id=\"$tabHash\">$contentConverted</div>";
                    }

                    $this->_tabsNames[$document->getTabName()]['pills'][$document->getOrigPillName()] = "<a hash-tab=\"$tabHash\" class=\"list-group-item wkdoc-pill-link$pillShow\" href=\"" . Url::current(['t' => $tabHash, 'p' => $pillHash]) . "\">{$document->getPillName()}</a>";
                }
            }
        }
    }
}