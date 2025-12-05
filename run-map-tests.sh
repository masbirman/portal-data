#!/bin/bash

echo "🧪 Running Interactive Map Visualization Tests..."
echo ""

echo "📊 Running Property-Based Tests (no database required)..."
php artisan test tests/PropertyBased/ComparisonModeTest.php --stop-on-failure
php artisan test tests/PropertyBased/LayerManagementTest.php --stop-on-failure
php artisan test tests/PropertyBased/LegendHighlightingTest.php --stop-on-failure
php artisan test tests/PropertyBased/PopupContentTest.php --stop-on-failure
php artisan test tests/PropertyBased/SearchFunctionalityTest.php --stop-on-failure
php artisan test tests/PropertyBased/StatisticsCalculatorTest.php --stop-on-failure

echo ""
echo "📊 Running Unit Tests..."
php artisan test tests/Unit/MapExportTest.php --stop-on-failure

echo ""
echo "✅ All tests completed!"
