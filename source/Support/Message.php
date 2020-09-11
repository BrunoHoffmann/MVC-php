<?php

namespace Source\Support;

class Message
{
    private $text;
    private $type;

    public function __toString()
    {
        return $this->render();
    }

    public function getText(): ?string 
    {
        return $this->text;
    }

    public function getType(): ?string 
    {
        return $this->type;
    }

    public function info(string $message): Message
    {
        $this->type = 'info';
        $this->text = $this->filter($message);
        return $this;
    }

    public function success(string $message): Message
    {
        $this->type = 'success';
        $this->text = $this->filter($message);
        return $this;
    }


    public function warning(string $message): Message 
    {
        $this->type = 'warning';
        $this->text = $this->filter($message);
        return $this;
    }


    public function error(string $message): Message 
    {
        $this->type = 'error';
        $this->text = $this->filter($message);
        return $this;
    }

    public function render(): string 
    {
        
        return "<div class='{$this->getType()}'>{$this->getText()}</div>";
        
    }

    private function filter(string $message): string 
    {
        return filter_var($message, FILTER_SANITIZE_STRIPPED);
    }
}