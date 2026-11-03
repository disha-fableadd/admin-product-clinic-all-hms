<?php

namespace App\Models;

/**
 * Product model aliases Medicine to provide seamless compatibility
 * with Pharmacy ERP workflows and Clinic ERP database.
 */
class Product extends Medicine
{
    // Inherits all attributes, fillables, casts, scopes, and relations from Medicine
}

