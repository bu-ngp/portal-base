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

/**
 * Класс контейнер документов для виджета [[\common\widgets\Documenter\Documenter]]
 */
class DocumenterContainer
{
    private $_documents;

    private $_tabsLinks = '';
    private $_tabsContent = '';
    private $_pillsContent = '';

    private $_tabsNames = [];

    private $_allowedTabsCount = 0;

    /**
     * @var string Шаблон ссылки вкладки документа.
     *
     * Содержит следующие маски:
     * * `tabActive` - Определяет активность вкладки документа.
     * * `tabId` - Содержимое id HTML атрибута вкладки.
     * * `tabName` - Имя вкладки документа.
     */
    public $tabLinkTemplate = '<li role="presentation"{tabActive}><a class="wkdoc-tab-link" href="#{tabId}" role="tab" data-toggle="tab">{tabName}</a></li>';
    /**
     * @var string Шаблон контента документа.
     *
     * Содержит следующие маски:
     * * `tabActive` - Определяет активность вкладки документа.
     * * `tabContent` - Содержимое документа.
     */
    public $tabContentTemplate = '<div role="tabpanel" class="tab-pane fade in{tabActive}" id="{tabId}">{tabContent}</div>';
    /**
     * @var string Шаблон ссылки плитки документа.
     *
     * Содержит следующие маски:
     * * `tabHash` - Хэш вкладки документа, к которому относится плитка.
     * * `pillActive` - Определяет активность плитки документа.
     * * `pillUrl` - Url плитки документа.
     * * `pillName` - Имя плитки документа.
     */
    public $pillContentTemplate = '<a hash-tab="{tabHash}" class="list-group-item wkdoc-pill-link{pillActive}" href="{pillUrl}">{pillName}</a>';

    /**
     * Конструктор класса.
     *
     * @param DocumenterViewer[] $documents Массив классов документов.
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

    /**
     * Возвращает контент всех плиток документов.
     *
     * @return string
     */
    public function getPillsContent()
    {
        return $this->_pillsContent;
    }

    /**
     * Возвращает контент всех ссылок вкладок документов.
     *
     * @return string
     */
    public function getTabsLinks()
    {
        return $this->_tabsLinks;
    }

    /**
     * Возвращает контент всех вкладок, относительно активной плитки.
     *
     * @return string
     */
    public function getTabsContent()
    {
        return $this->_tabsContent;
    }

    /**
     * Возвращает количество доступных вкладок документов, в зависимости от разрешений пользователя.
     *
     * @return int
     */
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