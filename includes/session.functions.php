<?php

function getMessagesShown(): bool
{
    return isset($_SESSION[MESSAGES_KEY]["shown"]);
}

function setMessagesShown(bool $shown): void
{
    $_SESSION[MESSAGES_KEY]["shown"] = $shown;
}

function getErrorMessage(): ?string
{
    return $_SESSION[MESSAGES_KEY]["error"] ?? null;
}

function setErrorMessage(string $message): void
{
    $_SESSION[MESSAGES_KEY]["error"] = $message;
}

function getSuccessMessage(): ?string
{
    return $_SESSION[MESSAGES_KEY]["success"] ?? null;
}

function setSuccessMessage(string $message): void
{
    $_SESSION[MESSAGES_KEY]["success"] = $message;
}

// Session

function setSessionToken(string $token): void
{
    $_SESSION["X-AUTH-TOKEN"] = $token;
}