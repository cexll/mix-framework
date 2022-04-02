<?php

namespace Mix\Framework\Database;

interface ModelInterface
{
    public function getOneId(int $id, array $columns = ['*']): array;

    public function findByWhere(array $where = [], array $columns = ['*'], array $options = []): array;

    public function getManyByIds(array $ids, array $columns = ['*']): array;

    public function getManyByWhere(array $where = [], array $columns = ['*'], array $options = []): array;

    public function getPageList(array $where = [], array $columns = ['*'], array $options = []): array;

    public function updateById(int $id): int;

    public function updateByIds(array $ids): int;

    public function updateByWhere(array $where): int;

    public function deleteOne(int $id): int;

    public function deleteAll(array $ids): int;

    public function deleteByWhere(array $where);

    public function createOne(array $data): int;

    public function createAll(array $data): int;

}