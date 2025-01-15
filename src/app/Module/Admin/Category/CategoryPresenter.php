<?php

declare(strict_types=1);

namespace App\Module\Admin\Category;


use Nette;
use App\Model\Category\CategoryFacade;
use App\Module\Admin\Accessory\Form\CategoryFormFactory;
use Nette\Application\UI\Form;
use Contributte\Datagrid\Datagrid;

final class CategoryPresenter extends \App\Module\Admin\BasePresenter
{

	/** @var \App\Model\Category\Category */
	private $category;

	/** @var CategoryFormFactory */
	private $categoryFormFactory;

	/** @var \App\Model\Category\CategoryRepository */
	private $categoryRepository;

	/** @var CategoryFacade */
	private $categoryFacade;

	public function injectCategoryForm(CategoryFormFactory $categoryFormFactory)
	{
		$this->categoryFormFactory = $categoryFormFactory;
		$this->categoryRepository = $this->em->getRepository(\App\Model\Category\Category::class);
	}

	public function injectCategoryFacade(CategoryFacade $categoryFacade)
	{
		$this->categoryFacade = $categoryFacade;
	}

	public function actionEdit(int $id)
	{
		$this->category = $this->categoryRepository->findOneById($id);

		if (!$this->category) {
			throw new \Exception('Category not found');
		}

		$tpl = $this->getTemplate();
		$tpl->setFile(__DIR__ . '/create.latte');
		$tpl->action = 'edit';
	}

	public function createComponentCategoryForm(): Form
	{
		$form = $this->categoryFormFactory->createCategoryForm($this->action, $this->category ?? null);

		$form->onSuccess[] = function(): void {
			$this->flashMessage('Kategorie byla úspěšně uložena');

			if ($this->action == 'create') {
				$lastCategory = $this->categoryRepository->findBy([], ['id' => 'DESC'], 1);
				$this->redirect('Category:edit', $lastCategory[0]->getId());
			} else {
				$this->redirect('this');
			}
		};
		return $form;
	}

	public function createComponentCategoryDatagrid($name)
	{
		$grid = new Datagrid($this, $name);
		$grid->setAutoSubmit(true); // Should be by default
		$grid->setTemplateFile(__DIR__ . '/../datagrid_override.latte');

		$grid->setDataSource($this->categoryRepository->createQueryBuilder('c'));
		$grid->setDefaultSort(['name' => 'ASC']);
		$grid->setDefaultPerPage(20);

		$grid->addColumnLink('name', 'Name', 'edit')->setClass('block hover:text-pink-600')->setSortable();
		$grid->addColumnText('parentId', 'Nadřazené kategorie');
		$grid->addColumnText('active', 'Aktivní')->setRenderer(function($item) {
			return $item->getActive() == 1 ? '✔️' : '❌';
		});
		$grid->addFilterText('name', 'Name')->setPlaceholder('Hledat dle názvu');

		$grid->addFilterSelect('parentId', '..', $this->categoryFacade->getAssociative())->setAttribute('class', 'select2');
	}

}
