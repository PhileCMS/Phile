<?php

namespace Phile\Template;

interface TemplateInterface {
	public function render();

	public function setCurrentPage(\Phile\Model\Page $page);
}
