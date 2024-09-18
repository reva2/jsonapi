<?php
/*
 * This file is part of the reva2/jsonapi.
 *
 * (c) Sergey Revenko <dedsemen@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */


namespace Reva2\JsonApi\Tests\Fixtures\Query;

use Reva2\JsonApi\Http\Query\ListQueryParameters;
use Reva2\JsonApi\Attributes as API;

/**
 * Query parameters for pets list API request
 *
 * @package Reva2\JsonApi\Tests\Fixtures\Query
 * @author Sergey Revenko <dedsemen@gmail.com>
 */
class PetsListQuery extends ListQueryParameters
{
    /**
     * Filter by pet family
     *
     * @var string
     */
    #[API\Property]
    protected $family;

    /**
     * @var int
     */
    #[API\Property]
    protected $store;

    /**
     * @return int
     */
    public function getStore()
    {
        return $this->store;
    }

    /**
     * @param int $store
     * @return $this
     */
    public function setStore($store)
    {
        $this->store = $store;

        return $this;
    }

    /**
     * @return string
     */
    public function getFamily()
    {
        return $this->family;
    }

    /**
     * @param string $family
     * @return $this
     */
    public function setFamily($family)
    {
        $this->family = $family;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getFilteringParameters(): ?array
    {
        $params = [];

        if (null !== $this->family) {
            $params['family'] = $this->family;
        }

        if (null !== $this->store) {
            $params['store'] = $this->store;
        }

        return (count($params) > 0) ? $params : null;
    }


    /**
     * @inheritdoc
     */
    protected function getAllowedIncludePaths(): array
    {
        return ['store'];
    }

    /**
     * @inheritdoc
     */
    protected function getAllowedFields($resource)
    {
        switch ($resource) {
            case 'pets':
                return ['name', 'family', 'store'];

            case 'stores':
                return ['name'];

            default:
                return parent::getAllowedFields($resource);
        }
    }

    /**
     * @inheritdoc
     */
    protected function getSortableFields(): array
    {
        return [
            'id',
            'name',
            'family',
            'store.id',
            'store.name'
        ];
    }
}