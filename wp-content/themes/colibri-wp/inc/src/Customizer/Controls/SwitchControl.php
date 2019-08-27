<?php


namespace ColibriWP\Theme\Customizer\Controls;


class SwitchControl extends VueControl {
	public $type = 'colibri-switch';

	protected $active_color            = "#1989fa";
	protected $inactive_color          = "#949596";
	protected $inline_content_template = true;

	public function json() {
		return array_merge( parent::json(), $this->getProps( array( 'active_color', 'inactive_color' ) ) );
	}

	protected function printVueContent() {
		?>
        <el-switch
                v-model="value"
                active-color="{{ data.active_color }}"
                inactive-color="{{ data.inactive_color }}"
                @change="setValue"
        >
        </el-switch>
		<?php
	}
}
