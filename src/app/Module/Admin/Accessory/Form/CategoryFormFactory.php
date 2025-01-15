<?php

namespace App\Module\Admin\Accessory\Form;

use \Nette\Application\UI\Form;
use App\Model\Category\CategoryFacade;
use App\Model\Category\Category;
class CategoryFormFactory
{

	private ?int $id;
	private string $action;
	private $categoryRepository;

	public function __construct(private \App\Core\Settings $settings, private CategoryFacade $categoryFacade) {}

	public function createCategoryForm(string $action, ?Category $category = null): Form
	{
		$this->action = $action;
		$this->id = $category ? $category->getId() : null;


		$form = new Form();
		$form->addText('name', 'Název')
			->setRequired('Zadejte název');

		$form->addSelect('parentId', 'Nadřazená kategorie', $this->categoryFacade->getAssociative());

		$form->addTextArea('description', 'Popis', 80, 18);

		$form->addCheckbox('active', 'Aktivní');

		if ($this->action == 'edit') {
			$form['name']->setDefaultValue($category->getName());
			$form['description']->setDefaultValue($category->getDescription());
			$form['active']->setDefaultValue($category->getActive());
			$form['parentId']->setDefaultValue($category->getParentId());
		}

		$form->onSuccess[] = [$this, 'formSubmitted'];
		$form->addSubmit('submit', 'Odeslat');
		return $form;
	}

	public function formSubmitted(Form $form, $data)
	{
		//bdump($data);exit;
		if (!$form->hasErrors()) {
			$this->categoryFacade->saveCategory($data, $this->id);
		}
	}

}