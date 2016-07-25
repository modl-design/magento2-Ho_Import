<?php
/**
 * Copyright (c) 2016 H&O E-commerce specialisten B.V. (http://www.h-o.nl/)
 * See LICENSE.txt for license details.
 */

namespace Ho\Import\RowModifier;

/**
 * @package Ho\Import
 */
class ItemMapper extends AbstractRowModifier
{

    /**
     * Mapping Definition
     * @var \Closure[]|string[]
     */
    protected $mappingDefinition;


    /**
     * Check if the product can be imported
     *
     * @param array $item
     * @param string $sku
     *
     * @return bool|\string[] return true if validated, return an array of errors when not valid.
     */
    public function validateItem(array $item, $sku)
    {
        return true;
    }

    /**
     * Retrieve the data.
     *
     * @return void
     */
    public function process()
    {
        foreach ($this->items as $sku => $item) {
            try {
                $errors = $this->validateItem($item, $sku);
                if ($errors === true) {
                    $this->items[$sku] = array_filter($this->mapItem($item));
                } else {
                    $this->consoleOutput->writeln(
                        sprintf("<comment>Error validating, skipping %s: %s</comment>", $sku, implode(",", $errors))
                    );
                }

            } catch (\Exception $e) {
                $this->consoleOutput->writeln("<error>{$e->getMessage()}</error>");
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function mapItem(array $item) : array
    {
        $product = [];
        foreach ($this->mappingDefinition as $key => $value) {
            if (is_callable($value)) {
                $value = $value($item);
            }

            $product[$key] = $value;
        }

        return $product;
    }


    /**
     * Set the mapping definition of the product.
     * @param \Closure[]|string[] $mappingDefinition
     * @return void
     */
    public function setMappingDefinition($mappingDefinition)
    {
        $this->mappingDefinition = $mappingDefinition;
    }
}