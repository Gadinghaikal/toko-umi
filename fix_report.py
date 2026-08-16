import re

path = r'c:\Users\Haikal\Documents\COBA COBA\TOKO\app\Http\Controllers\ReportController.php'
with open(path, 'r', encoding='utf-8') as f:
    content = f.read()

# Replace total_amount with grand_total
content = content.replace("->sum('total_amount')", "->sum('grand_total')")
content = content.replace("->total_amount", "->grand_total")

# Replace profit calculation for daily (Lines 31-40)
old_profit_1 = """        // Kalkulasi profit: total_amount - sum(price_buy * qty)
        $totalProfit = 0;
        foreach ($transactions as $trx) {
            foreach ($trx->items as $item) {
                if ($item->product) {
                    $profit = ($item->price - $item->product->price_buy) * $item->quantity;
                    $totalProfit += $profit;
                }
            }
        }"""
new_profit_1 = """        // Kalkulasi profit: total_amount - sum(price_buy * qty)
        $totalProfit = 0;
        foreach ($transactions as $trx) {
            foreach ($trx->items as $item) {
                $totalProfit += $item->total_profit;
            }
        }"""
content = content.replace(old_profit_1, new_profit_1)

# Replace profit calculation for monthly
old_profit_2 = """            $trxProfit = 0;
            foreach ($trx->items as $item) {
                if ($item->product) {
                    $trxProfit += ($item->price - $item->product->price_buy) * $item->quantity;
                }
            }"""
new_profit_2 = """            $trxProfit = 0;
            foreach ($trx->items as $item) {
                $trxProfit += $item->total_profit;
            }"""
content = content.replace(old_profit_2, new_profit_2)

# Replace profit calculation for exportPdf
old_profit_3 = """            $totalProfit = 0;
            foreach ($transactions as $trx) {
                foreach ($trx->items as $item) {
                    if ($item->product) $totalProfit += ($item->price - $item->product->price_buy) * $item->quantity;
                }
            }"""
new_profit_3 = """            $totalProfit = 0;
            foreach ($transactions as $trx) {
                foreach ($trx->items as $item) {
                    $totalProfit += $item->total_profit;
                }
            }"""
content = content.replace(old_profit_3, new_profit_3)

# Replace customer_name
content = content.replace("$trx->customer_name", "$trx->notes")

with open(path, 'w', encoding='utf-8') as f:
    f.write(content)
print('Done!')
