'use client'

import * as React from 'react';
import Link from 'next/link';
import {
    ShoppingBag,
    ArrowRight,
    Trash2,
    RefreshCw,
    AlertTriangle,
    ArrowLeft,
    Package,
} from 'lucide-react';
import {
    Button,
    Card,
    CardContent,
    CardHeader,
    CardTitle,
    Dialog,
    DialogContent,
    DialogHeader,
    DialogTitle,
} from '@/components/ui';
import { DashboardLayout } from '@/components/layout';
import { CartItem } from '@/components/cart/CartItem';
import { useCartStore, useCartItems, useCartTotal, useCartItemCount } from '@/stores/cartStore';
import { ProductGrid } from '@/components/product/ProductGrid';
import { useProductStore } from '@/stores/productStore';
import { RouteGuard } from '@/components/auth/RouteGuard';
import { cn } from '@/lib/cn';

function CartPage() {
    const {
        clearCart,
        syncCartPrices,
        isLoading,
        error,
    } = useCartStore();

    const items = useCartItems();
    const total = useCartTotal();
    const itemCount = useCartItemCount();

    const { products: relatedProducts, fetchProducts } = useProductStore();

    const [showClearConfirm, setShowClearConfirm] = React.useState(false);

    // Fetch related/recommended products
    React.useEffect(() => {
        fetchProducts({ featured: true, per_page: 4 });
    }, [fetchProducts]);

    const handleClearCart = async () => {
        try {
            await clearCart();
            setShowClearConfirm(false);
        } catch (error) {
            console.error('Failed to clear cart:', error);
        }
    };

    const handleSyncPrices = async () => {
        try {
            await syncCartPrices();
        } catch (error) {
            console.error('Failed to sync prices:', error);
        }
    };

    const hasItems = items.length > 0;
    const hasIssues = items.some(item => !item.is_available || item.has_price_changed);
    const shippingThreshold = 5000; // £50 in pennies
    const needsForFreeShipping = Math.max(0, shippingThreshold - total.amount);

    return (
        <RouteGuard requireAuth>
            <DashboardLayout
                title="Shopping Cart"
                description="Review your items and proceed to checkout"
                showBreadcrumbs
            >
                <div className="space-y-8">
                    {/* Back to Shopping */}
                    <div className="flex items-center justify-between">
                        <Link href="/products">
                            <Button variant="ghost" className="gap-2">
                                <ArrowLeft className="h-4 w-4" />
                                Continue Shopping
                            </Button>
                        </Link>

                        {hasItems && (
                            <div className="text-sm text-muted-foreground">
                                {itemCount} item{itemCount !== 1 ? 's' : ''} in cart
                            </div>
                        )}
                    </div>

                    {/* Error Banner */}
                    {error && (
                        <Card className="border-destructive/50 bg-destructive/5">
                            <CardContent className="p-4">
                                <div className="flex items-center gap-2">
                                    <AlertTriangle className="h-5 w-5 text-destructive" />
                                    <p className="text-destructive">{error}</p>
                                </div>
                            </CardContent>
                        </Card>
                    )}

                    {/* Issues Banner */}
                    {hasIssues && (
                        <Card className="border-warning/50 bg-warning/5">
                            <CardContent className="p-4">
                                <div className="flex items-center justify-between">
                                    <div className="flex items-center gap-2">
                                        <AlertTriangle className="h-5 w-5 text-warning" />
                                        <div>
                                            <p className="font-medium text-warning">Cart Issues Detected</p>
                                            <p className="text-sm text-warning/80">
                                                Some items have price changes or are out of stock
                                            </p>
                                        </div>
                                    </div>
                                    <Button
                                        variant="outline"
                                        onClick={handleSyncPrices}
                                        disabled={isLoading}
                                        className="border-warning text-warning hover:bg-warning hover:text-warning-foreground"
                                    >
                                        <RefreshCw className={cn(
                                            "h-4 w-4 mr-2",
                                            isLoading && "animate-spin"
                                        )} />
                                        Update Prices
                                    </Button>
                                </div>
                            </CardContent>
                        </Card>
                    )}

                    {/* Main Content */}
                    {isLoading && items.length === 0 ? (
                        <Card>
                            <CardContent className="flex items-center justify-center h-64">
                                <div className="text-center space-y-4">
                                    <RefreshCw className="h-8 w-8 animate-spin mx-auto text-muted-foreground" />
                                    <p className="text-muted-foreground">Loading your cart...</p>
                                </div>
                            </CardContent>
                        </Card>
                    ) : !hasItems ? (
                        <EmptyCartState />
                    ) : (
                        <div className="grid lg:grid-cols-3 gap-8">
                            {/* Cart Items */}
                            <div className="lg:col-span-2 space-y-6">
                                <Card>
                                    <CardHeader className="flex flex-row items-center justify-between">
                                        <CardTitle className="flex items-center gap-2">
                                            <ShoppingBag className="h-5 w-5" />
                                            Your Items ({itemCount})
                                        </CardTitle>

                                        {hasItems && (
                                            <Button
                                                variant="ghost"
                                                size="sm"
                                                onClick={() => setShowClearConfirm(true)}
                                                disabled={isLoading}
                                                className="text-muted-foreground hover:text-destructive"
                                            >
                                                <Trash2 className="h-4 w-4 mr-2" />
                                                Clear Cart
                                            </Button>
                                        )}
                                    </CardHeader>
                                    <CardContent className="space-y-4">
                                        {items.map((item) => (
                                            <CartItem
                                                key={`${item.product_id}-${item.product_variant_id || 'default'}`}
                                                item={item}
                                                layout="default"
                                            />
                                        ))}
                                    </CardContent>
                                </Card>
                            </div>

                            {/* Order Summary */}
                            <div className="space-y-6">
                                <Card className="sticky top-24">
                                    <CardHeader>
                                        <CardTitle>Order Summary</CardTitle>
                                    </CardHeader>
                                    <CardContent className="space-y-4">
                                        {/* Subtotal */}
                                        <div className="flex items-center justify-between">
                                            <span>Subtotal ({itemCount} items)</span>
                                            <span className="font-medium">{total.formatted}</span>
                                        </div>

                                        {/* Shipping */}
                                        <div className="flex items-center justify-between">
                                            <span>Shipping</span>
                                            <span className="font-medium">
                                                {total.amount >= shippingThreshold ? (
                                                    <span className="text-success">Free</span>
                                                ) : (
                                                    'Calculated at checkout'
                                                )}
                                            </span>
                                        </div>

                                        {/* Free Shipping Progress */}
                                        {needsForFreeShipping > 0 && (
                                            <div className="space-y-2">
                                                <div className="flex items-center justify-between text-sm">
                                                    <span className="text-muted-foreground">
                                                        Add £{(needsForFreeShipping / 100).toFixed(2)} for free shipping
                                                    </span>
                                                </div>
                                                <div className="w-full bg-muted h-2 rounded-full overflow-hidden">
                                                    <div
                                                        className="h-full bg-primary transition-all duration-300"
                                                        style={{
                                                            width: `${Math.min(100, (total.amount / shippingThreshold) * 100)}%`
                                                        }}
                                                    />
                                                </div>
                                            </div>
                                        )}

                                        <div className="border-t pt-4">
                                            <div className="flex items-center justify-between text-lg font-bold">
                                                <span>Total</span>
                                                <span className="text-primary">{total.formatted}</span>
                                            </div>
                                        </div>

                                        {/* Checkout Button */}
                                        <div className="space-y-3">
                                            <Link href="/checkout" className="block">
                                                <Button
                                                    className="w-full"
                                                    size="lg"
                                                    disabled={!hasItems || hasIssues}
                                                >
                                                    Proceed to Checkout
                                                    <ArrowRight className="h-4 w-4 ml-2" />
                                                </Button>
                                            </Link>

                                            <Link href="/products" className="block">
                                                <Button variant="outline" className="w-full">
                                                    Continue Shopping
                                                </Button>
                                            </Link>
                                        </div>

                                        {/* Trust Signals */}
                                        <div className="pt-4 border-t space-y-2 text-xs text-muted-foreground">
                                            <div className="flex items-center gap-2">
                                                <Package className="h-3 w-3" />
                                                <span>Free shipping on orders over £50</span>
                                            </div>
                                            <div className="flex items-center gap-2">
                                                <Package className="h-3 w-3" />
                                                <span>Secure checkout & payment</span>
                                            </div>
                                            <div className="flex items-center gap-2">
                                                <Package className="h-3 w-3" />
                                                <span>30-day return guarantee</span>
                                            </div>
                                        </div>
                                    </CardContent>
                                </Card>
                            </div>
                        </div>
                    )}

                    {/* Recommended Products */}
                    {hasItems && relatedProducts.length > 0 && (
                        <Card>
                            <CardHeader>
                                <CardTitle>You Might Also Like</CardTitle>
                            </CardHeader>
                            <CardContent>
                                <ProductGrid
                                    products={relatedProducts}
                                    columns={{ sm: 1, md: 2, lg: 3, xl: 4 }}
                                    showFilters={false}
                                    showSort={false}
                                />
                            </CardContent>
                        </Card>
                    )}
                </div>

                {/* Clear Cart Confirmation Dialog */}
                <Dialog open={showClearConfirm} onOpenChange={setShowClearConfirm}>
                    <DialogContent>
                        <DialogHeader>
                            <DialogTitle>Clear Cart?</DialogTitle>
                        </DialogHeader>
                        <div className="space-y-4">
                            <p className="text-muted-foreground">
                                Are you sure you want to remove all {itemCount} item{itemCount !== 1 ? 's' : ''} from your cart? This action cannot be undone.
                            </p>
                            <div className="flex gap-3 justify-end">
                                <Button
                                    variant="outline"
                                    onClick={() => setShowClearConfirm(false)}
                                >
                                    Cancel
                                </Button>
                                <Button
                                    variant="destructive"
                                    onClick={handleClearCart}
                                    disabled={isLoading}
                                >
                                    {isLoading ? 'Clearing...' : 'Clear Cart'}
                                </Button>
                            </div>
                        </div>
                    </DialogContent>
                </Dialog>
            </DashboardLayout>
        </RouteGuard>
    );
}

// Empty Cart State Component
function EmptyCartState() {
    return (
        <Card>
            <CardContent className="flex flex-col items-center justify-center py-16">
                <div className="w-32 h-32 bg-muted/50 rounded-full flex items-center justify-center mb-6">
                    <ShoppingBag className="h-16 w-16 text-muted-foreground" />
                </div>

                <h2 className="text-2xl font-bold text-foreground mb-2">
                    Your cart is empty
                </h2>

                <p className="text-muted-foreground text-center max-w-md mb-8">
                    Looks like you haven't added anything to your cart yet.
                    Start browsing our creative products to build your perfect order!
                </p>

                <div className="flex gap-4">
                    <Link href="/products">
                        <Button size="lg">
                            <Package className="h-4 w-4 mr-2" />
                            Browse Products
                        </Button>
                    </Link>

                    <Link href="/products?featured=true">
                        <Button variant="outline" size="lg">
                            View Featured Items
                        </Button>
                    </Link>
                </div>

                {/* Popular Categories */}
                <div className="mt-12 w-full max-w-2xl">
                    <h3 className="text-lg font-semibold text-center mb-6">
                        Popular Categories
                    </h3>
                    <div className="grid grid-cols-2 md:grid-cols-4 gap-4">
                        {[
                            { name: 'Labels', href: '/products?category=labels' },
                            { name: 'Invitations', href: '/products?category=invitations' },
                            { name: 'Gift Tags', href: '/products?category=gift-tags' },
                            { name: 'Stickers', href: '/products?category=stickers' },
                        ].map((category) => (
                            <Link key={category.name} href={category.href}>
                                <Card className="hover:bg-muted/50 transition-colors cursor-pointer">
                                    <CardContent className="p-4 text-center">
                                        <h4 className="font-medium">{category.name}</h4>
                                    </CardContent>
                                </Card>
                            </Link>
                        ))}
                    </div>
                </div>
            </CardContent>
        </Card>
    );
}

export default CartPage;
