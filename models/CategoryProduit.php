<?php

class CategoryProduit
{
    private int $categoryId;
    private string $categoryName;
    private string $categorylogo;


    /**
     * @param int $categoryId
     * @param string $categoryName

     */
    public function __construct(int $categoryId, string $categoryName, string $categorylogo)
    {
        $this->categoryId = $categoryId;
        $this->categoryName = $categoryName;
        $this->categorylogo = $categorylogo;

    }

    /**
     * @return int
     */
    public function getCategoryId(): int
    {
        return $this->categoryId;
    }

    /**
     * @param int $categoryId
     */
    public function setCategoryId(int $categoryId): void
    {
        $this->categoryId = $categoryId;
    }

    /**
     * @return string
     */
    public function getCategoryName(): string
    {
        return $this->categoryName;
    }

    /**
     * @param string $categoryName
     */
    public function setCategoryName(string $categoryName): void
    {
        $this->categoryName = $categoryName;
    }

    /**
     * @return string
     */
    public function getCategorylogo(): string
    {
        return $this->categorylogo;
    }

    /**
     * @param string $categorylogo
     */
    public function setCategorylogo(string $categorylogo): void
    {
        $this->categorylogo = $categorylogo;
    }



}