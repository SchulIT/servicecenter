<?php

namespace App\Doctrine;

use Doctrine\ORM\Query\AST\Functions\FunctionNode;
use Doctrine\ORM\Query\Parser;
use Doctrine\ORM\Query\SqlWalker;
use Doctrine\ORM\Query\TokenType;
use Override;

class NaturalSortKey extends FunctionNode {
    protected $target;

    #[Override]
    public function parse(Parser $parser): void {
        $parser->match(TokenType::T_IDENTIFIER);
        $parser->match(TokenType::T_OPEN_PARENTHESIS);

        $this->target = $parser->StringPrimary();

        $parser->match(TokenType::T_CLOSE_PARENTHESIS);
    }

    #[Override]
    public function getSql(SqlWalker $sqlWalker): string {
        $target = $sqlWalker->walkStringPrimary($this->target);

        return sprintf('NATURAL_SORT_KEY(%s)', $target);
    }
}