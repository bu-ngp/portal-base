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
    private $_documents;

    private $_tabsLinks = '';
    private $_tabsContent = '';
    private $_pillsContent = '';

    private $_tabsNames = [];

    private $_allowedTabsCount = 0;

    public $tabLinkTemplate = '<li role="presentation"{tabActive}><a class="wkdoc-tab-link" href="#{tabId}" role="tab" data-toggle="tab">{tabName}</a></li>';
    public $tabContentTemplate = '<div role="tabpanel" class="tab-pane fade in{tabActive}" id="{tabId}">{tabContent}</div>';
    public $pillContentTemplate = '<a hash-tab="{tabHash}" class="list-group-item wkdoc-pill-link{pillActive}" href="{pillUrl}">{pillName}</a>';

    /**
     * @param DocumenterViewer[] $documents
     */
    public function __construct(array $documents)
    {
        $this->_documents = $documents;
        $this->initTabs();

        $content = [];
        array_walk($this->_tabsNames, function ($tabs) use (&$content) {
            $content['tabLink'] .= $tabs['tabLink'];
            $content['tabContent'] .= $tabs['tabContent'];
            $content['pills'] .= implode('', $tabs['pills']);
        });
        $this->_tabsLinks = $content['tabLink'];
        $this->_tabsContent = $content['tabContent'];
        $this->_pillsContent = $content['pills'];
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

    public function allowedTabsCount()
    {
        return $this->_allowedTabsCount;
    }

    protected function initTabs()
    {
        /** @var DocumenterViewer[] $viewers */
        foreach ($this->_documents as $directory => $viewers) {
            $this->sortViewers($viewers);

            foreach ($viewers as $key => $document) {
                if ($document->isAllowed()) {
                    $pillShow = '';
                    $active = '';
                    $activeTabContent = '';

                    $tabHash = $document->getTabHash();
                    $pillHash = $document->getPillHash();

                    if (!isset($this->_tabsNames[$document->getTabName()])) {
                        $pillShow = ' wkdoc-pill-hide';
                        $this->_allowedTabsCount++;

                        if ($key === 0) {
                            $active = ' class="active" ';
                            $pillShow = ' wkdoc-pill-show';
                            $activeTabContent = ' active';
                        }

                        $this->_tabsNames[$document->getTabName()] = [
                            'tabLink' => strtr($this->tabLinkTemplate, [
                                '{tabActive}' => $active,
                                '{tabId}' => $tabHash,
                                '{tabName}' => $document->getTabName(),
                            ]),
                        ];

                        $this->_tabsNames[$document->getTabName()]['tabContent'] = strtr($this->tabContentTemplate, [
                            '{tabActive}' => $activeTabContent,
                            '{tabId}' => $tabHash,
                            '{tabContent}' => Markdown::convert($document->getContent()),
                        ]);
                    }

                    $this->_tabsNames[$document->getTabName()]['pills'][$document->getOrigPillName()] = strtr($this->pillContentTemplate, [
                        '{tabHash}' => $tabHash,
                        '{pillActive}' => $pillShow,
                        '{pillUrl}' => Url::current(['t' => $tabHash, 'p' => $pillHash]),
                        '{pillName}' => $document->getPillName(),
                    ]);
                }
            }
        }
    }

    /**
     * @param DocumenterViewer[] $viewers
     */
    protected function sortViewers(array &$viewers)
    {
        usort($viewers, function (DocumenterViewer $a, DocumenterViewer $b) {
            if ($a->getTabName() === $b->getTabName() && $a->getOrigPillName() === $b->getOrigPillName()) {
                return 0;
            }

            if ($a->getTabName() === $b->getTabName()) {
                return $a->getOrigPillName() > $b->getOrigPillName() ? -1 : 1;
            }

            return ($a->getTabName() > $b->getTabName() && $a->getOrigPillName() > $b->getOrigPillName()) ? -1 : 1;
        });
    }
}