<?php

namespace Database\Seeders;

use App\Models\Page;
use Illuminate\Database\Seeder;

class PageSeeder extends Seeder
{
    public function run(): void
    {
        $pages = [
            [
                'title' => 'FAQ',
                'slug' => 'faq',
                'content' => '<div class="faq-page">
                    <h2>Frequently Asked Questions</h2>
                    <p class="intro">Find answers to the most common questions about shopping with Shirin Fashion. If you cannot find the answer you are looking for, please feel free to contact our customer service team.</p>
                    
                    <div class="faq-section">
                        <h3>General Questions</h3>
                        
                        <div class="faq-item">
                            <h4>How do I place an order?</h4>
                            <p>Placing an order on Shirin Fashion is simple! Browse our collection, select your desired products, choose your size and color, add to cart, and proceed to checkout. Follow the easy steps to complete your purchase.</p>
                        </div>
                        
                        <div class="faq-item">
                            <h4>Do I need to create an account to shop?</h4>
                            <p>While you can checkout as a guest, creating an account offers benefits like order tracking, faster checkout, wishlist saving, and exclusive deals.</p>
                        </div>
                        
                        <div class="faq-item">
                            <h4>How can I track my order?</h4>
                            <p>Once your order is shipped, you will receive an email with a tracking number. You can also log into your account and visit the "My Orders" section to track your package in real-time.</p>
                        </div>
                        
                        <div class="faq-item">
                            <h4>What payment methods do you accept?</h4>
                            <p>We accept all major credit cards (Visa, Mastercard, American Express), bKash, Nagad, Rocket, and cash on delivery for eligible areas.</p>
                        </div>
                    </div>
                    
                    <div class="faq-section">
                        <h3>Shipping & Delivery</h3>
                        
                        <div class="faq-item">
                            <h4>How long does delivery take?</h4>
                            <p>Standard delivery takes 3-7 business days within Bangladesh. Express delivery is available in major cities and takes 1-3 business days.</p>
                        </div>
                        
                        <div class="faq-item">
                            <h4>Do you offer international shipping?</h4>
                            <p>Yes, we ship internationally to select countries. Delivery time varies by location. Additional shipping charges may apply for international orders.</p>
                        </div>
                        
                        <div class="faq-item">
                            <h4>How much does shipping cost?</h4>
                            <p>Shipping is free for orders above ৳2000 within Bangladesh. Standard shipping is ৳120 for orders below ৳2000. Express shipping rates vary by location.</p>
                        </div>
                    </div>
                    
                    <div class="faq-section">
                        <h3>Returns & Exchanges</h3>
                        
                        <div class="faq-item">
                            <h4>What is your return policy?</h4>
                            <p>We offer a 7-day return policy for most items. Products must be unused, in original packaging, and tags attached. Some items like cosmetics and inner wear are not eligible for return due to hygiene reasons.</p>
                        </div>
                        
                        <div class="faq-item">
                            <h4>How do I return a product?</h4>
                            <p>Log into your account, go to "My Orders", select the order, choose the item you want to return, and submit a return request. Our team will contact you for pickup.</p>
                        </div>
                        
                        <div class="faq-item">
                            <h4>When will I get my refund?</h4>
                            <p>Once we receive and inspect your returned item, refunds are processed within 5-7 business days. The amount will be credited to your original payment method or store credit.</p>
                        </div>
                    </div>
                    
                    <div class="faq-section">
                        <h3>Product & Sizing</h3>
                        
                        <div class="faq-item">
                            <h4>How do I find my correct size?</h4>
                            <p>Each product has a detailed size chart in the product description. Measure yourself and compare with our chart. If between sizes, we recommend sizing up for a comfortable fit.</p>
                        </div>
                        
                        <div class="faq-item">
                            <h4>Are the colors accurate to the pictures?</h4>
                            <p>We strive to display colors as accurately as possible. However, actual colors may vary slightly due to monitor settings and photography lighting.</p>
                        </div>
                        
                        <div class="faq-item">
                            <h4>How do I care for my products?</h4>
                            <p>Care instructions are provided on each product label. Generally, we recommend gentle machine wash or hand wash with mild detergent. Avoid direct sunlight for drying.</p>
                        </div>
                    </div>
                    
                    <div class="faq-section">
                        <h3>Contact Us</h3>
                        <p>Still have questions? Our customer service team is here to help!</p>
                        <ul>
                            <li><strong>Email:</strong> support@shirinfashion.com</li>
                            <li><strong>Phone:</strong> +880 1XXX-XXXXXX (Available 9 AM - 10 PM)</li>
                            <li><strong>WhatsApp:</strong> +880 1XXX-XXXXXX</li>
                        </ul>
                    </div>
                </div>',
                'is_active' => true,
                'sort_order' => 1,
            ],
            [
                'title' => 'Shipping Information',
                'slug' => 'shipping',
                'content' => '<div class="shipping-page">
                    <h2>Shipping Information</h2>
                    <p class="intro">At Shirin Fashion, we are committed to delivering your orders safely and on time. Please read our shipping policies below.</p>
                    
                    <div class="shipping-section">
                        <h3>Delivery Timeframes</h3>
                        
                        <div class="delivery-option">
                            <h4>Standard Delivery</h4>
                            <p><strong>Time:</strong> 3-7 business days</p>
                            <p><strong>Cost:</strong> ৳120 (Free for orders above ৳2000)</p>
                            <p>Available across all districts in Bangladesh</p>
                        </div>
                        
                        <div class="delivery-option">
                            <h4>Express Delivery</h4>
                            <p><strong>Time:</strong> 1-3 business days</p>
                            <p><strong>Cost:</strong> ৳250</p>
                            <p>Available in Dhaka, Chattogram, Sylhet, Khulna, and Rajshahi</p>
                        </div>
                        
                        <div class="delivery-option">
                            <h4>Same Day Delivery</h4>
                            <p><strong>Time:</strong> Within 8 hours</p>
                            <p><strong>Cost:</strong> ৳350</p>
                            <p>Available within Dhaka city only</p>
                        </div>
                    </div>
                    
                    <div class="shipping-section">
                        <h3>International Shipping</h3>
                        <p>We ship to the following countries:</p>
                        <ul>
                            <li>India, Nepal, Bhutan, Sri Lanka</li>
                            <li>USA, UK, Canada, Australia</li>
                            <li>United Arab Emirates, Saudi Arabia, Qatar</li>
                        </ul>
                        <p><strong>Delivery Time:</strong> 10-21 business days</p>
                        <p><strong>Shipping Cost:</strong> Calculated at checkout based on weight and destination</p>
                    </div>
                    
                    <div class="shipping-section">
                        <h3>Order Processing</h3>
                        <ul>
                            <li>Orders placed before 12 PM are processed same-day</li>
                            <li>Orders placed after 12 PM are processed next business day</li>
                            <li>During sale seasons, processing may take 1-2 extra days</li>
                            <li>You will receive order confirmation via email and SMS</li>
                        </ul>
                    </div>
                    
                    <div class="shipping-section">
                        <h3>Tracking Your Order</h3>
                        <p>Once your order is shipped, you will receive:</p>
                        <ul>
                            <li>Tracking number via SMS and email</li>
                            <li>Link to track your package in real-time</li>
                            <li>Delivery updates from our courier partner</li>
                        </ul>
                        <p>You can also track your order through your Shirin Fashion account.</p>
                    </div>
                    
                    <div class="shipping-section">
                        <h3>Shipping Restrictions</h3>
                        <ul>
                            <li>Some products may require additional delivery time due to customization</li>
                            <li>Remote areas may have extended delivery times</li>
                            <li>Holidays and adverse weather conditions may affect delivery schedules</li>
                        </ul>
                    </div>
                    
                    <div class="shipping-section">
                        <h3>Contact Us</h3>
                        <p>For shipping inquiries: <strong>shipping@shirinfashion.com</strong></p>
                    </div>
                </div>',
                'is_active' => true,
                'sort_order' => 2,
            ],
            [
                'title' => 'Returns & Exchanges',
                'slug' => 'returns',
                'content' => '<div class="returns-page">
                    <h2>Returns & Exchanges Policy</h2>
                    <p class="intro">We want you to love your purchase from Shirin Fashion. If for any reason you are not completely satisfied, we offer a flexible return policy.</p>
                    
                    <div class="returns-section">
                        <h3>Return Policy Overview</h3>
                        <ul>
                            <li><strong>Return Window:</strong> 7 days from delivery date</li>
                            <li><strong>Condition:</strong> Items must be unused, unwashed, and with original tags attached</li>
                            <li><strong>Refund Method:</strong> Original payment method or store credit</li>
                            <li><strong>Return Shipping:</strong> Free for defective items; ৳100 for other returns</li>
                        </ul>
                    </div>
                    
                    <div class="returns-section">
                        <h3>Eligible for Return</h3>
                        <ul>
                            <li>Wrong size received (different from ordered)</li>
                            <li>Defective or damaged product</li>
                            <li>Wrong color received</li>
                            <li>Missing items from order</li>
                            <li>Quality issues (fabric defects, stitching problems)</li>
                        </ul>
                    </div>
                    
                    <div class="returns-section">
                        <h3>Not Eligible for Return</h3>
                        <ul>
                            <li>Inner wear, lingerie, and swimwear (hygiene reasons)</li>
                            <li>Cosmetics and beauty products (seal broken)</li>
                            <li>Customized or personalized products</li>
                            <li>Sale items (unless defective)</li>
                            <li>Items with obvious signs of use</li>
                            <li>Accessories (jewelry, bags with tags removed)</li>
                        </ul>
                    </div>
                    
                    <div class="returns-section">
                        <h3>How to Return</h3>
                        <div class="steps">
                            <div class="step">
                                <span class="step-number">1</span>
                                <h4>Submit Request</h4>
                                <p>Log into your account, go to "My Orders", select the order, and click "Return Item"</p>
                            </div>
                            <div class="step">
                                <span class="step-number">2</span>
                                <h4>Fill Form</h4>
                                <p>Provide details about the issue and upload photos if applicable</p>
                            </div>
                            <div class="step">
                                <span class="step-number">3</span>
                                <h4>Wait for Confirmation</h4>
                                <p>Our team will review and confirm your return within 24-48 hours</p>
                            </div>
                            <div class="step">
                                <span class="step-number">4</span>
                                <h4>Ship Back</h4>
                                <p>Pack the item securely and our courier will collect it</p>
                            </div>
                            <div class="step">
                                <span class="step-number">5</span>
                                <h4>Get Refund</h4>
                                <p>Once inspected, refund is processed within 5-7 business days</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="returns-section">
                        <h3>Refund Timeline</h3>
                        <table class="refund-table">
                            <tr>
                                <th>Refund Method</th>
                                <th>Processing Time</th>
                                <th>Arrival Time</th>
                            </tr>
                            <tr>
                                <td>Original Payment Method</td>
                                <td>5-7 business days</td>
                                <td>7-14 business days</td>
                            </tr>
                            <tr>
                                <td>Store Credit</td>
                                <td>1-2 business days</td>
                                <td>Instant</td>
                            </tr>
                            <tr>
                                <td>Bank Transfer</td>
                                <td>5-7 business days</td>
                                <td>7-10 business days</td>
                            </tr>
                        </table>
                    </div>
                    
                    <div class="returns-section">
                        <h3>Exchange Policy</h3>
                        <p>We offer size exchanges for the same product in a different size. Process is the same as returns - just select "Exchange" instead of "Return" when submitting your request.</p>
                        <ul>
                            <li>Exchange shipping is free</li>
                            <li>Price difference will be adjusted</li>
                            <li>Subject to availability</li>
                        </ul>
                    </div>
                    
                    <div class="returns-section">
                        <h3>Damaged or Defective Items</h3>
                        <p>If you receive a damaged or defective item, we apologize! Please:</p>
                        <ul>
                            <li>Take photos of the damage/defect</li>
                            <li>Submit return request within 7 days</li>
                            <li>We will arrange free return shipping</li>
                            <li>Full refund or replacement at your choice</li>
                        </ul>
                    </div>
                    
                    <div class="returns-section">
                        <h3>Contact Us</h3>
                        <p>For return inquiries: <strong>returns@shirinfashion.com</strong></p>
                        <p>Phone: +880 1XXX-XXXXXX (9 AM - 10 PM)</p>
                    </div>
                </div>',
                'is_active' => true,
                'sort_order' => 3,
            ],
            [
                'title' => 'Privacy Policy',
                'slug' => 'privacy-policy',
                'content' => '<div class="privacy-page">
                    <h2>Privacy Policy</h2>
                    <p class="intro">At Shirin Fashion, we take your privacy seriously. This Privacy Policy explains how we collect, use, disclose, and safeguard your information when you visit our website.</p>
                    
                    <div class="privacy-section">
                        <h3>Information We Collect</h3>
                        
                        <h4>Personal Information</h4>
                        <ul>
                            <li>Name and contact information (email, phone, address)</li>
                            <li>Payment information (processed securely through our payment partners)</li>
                            <li>Account credentials (username, password)</li>
                            <li>Order history and preferences</li>
                            <li>Communication history with our customer service</li>
                        </ul>
                        
                        <h4>Automatically Collected Information</h4>
                        <ul>
                            <li>Device information (IP address, browser type, operating system)</li>
                            <li>Usage data (pages visited, time spent, links clicked)</li>
                            <li>Location data (general location based on IP)</li>
                            <li>Cookies and similar tracking technologies</li>
                        </ul>
                    </div>
                    
                    <div class="privacy-section">
                        <h3>How We Use Your Information</h3>
                        <ul>
                            <li><strong>Order Processing:</strong> To process and fulfill your orders</li>
                            <li><strong>Account Management:</strong> To create and manage your account</li>
                            <li><strong>Personalization:</strong> To customize your shopping experience</li>
                            <li><strong>Communication:</strong> To send order updates, promotional offers, and newsletters</li>
                            <li><strong>Marketing:</strong> To provide targeted advertisements based on your interests</li>
                            <li><strong>Analytics:</strong> To analyze usage patterns and improve our services</li>
                            <li><strong>Fraud Prevention:</strong> To detect and prevent fraudulent transactions</li>
                            <li><strong>Legal Compliance:</strong> To comply with applicable laws and regulations</li>
                        </ul>
                    </div>
                    
                    <div class="privacy-section">
                        <h3>Information Sharing</h3>
                        <p>We may share your information with:</p>
                        <ul>
                            <li><strong>Service Providers:</strong> Payment processors, shipping partners, email service providers</li>
                            <li><strong>Business Partners:</strong> For joint promotions (with your consent)</li>
                            <li><strong>Legal Authorities:</strong> When required by law or to protect our rights</li>
                            <li><strong>Analytics Providers:</strong> Google Analytics and similar services</li>
                        </ul>
                        <p class="note">We never sell your personal information to third parties.</p>
                    </div>
                    
                    <div class="privacy-section">
                        <h3>Data Security</h3>
                        <ul>
                            <li>SSL encryption for all data transmission</li>
                            <li>Secure payment processing (PCI DSS compliant)</li>
                            <li>Regular security audits and updates</li>
                            <li>Access controls and employee training</li>
                            <li>Encrypted data storage</li>
                        </ul>
                    </div>
                    
                    <div class="privacy-section">
                        <h3>Your Rights</h3>
                        <p>You have the right to:</p>
                        <ul>
                            <li><strong>Access:</strong> Request a copy of your personal data</li>
                            <li><strong>Rectification:</strong> Correct inaccurate personal data</li>
                            <li><strong>Deletion:</strong> Request deletion of your account and data</li>
                            <li><strong>Opt-out:</strong> Unsubscribe from marketing communications</li>
                            <li><strong>Portability:</strong> Request your data in a portable format</li>
                        </ul>
                    </div>
                    
                    <div class="privacy-section">
                        <h3>Cookies Policy</h3>
                        <p>We use cookies to:</p>
                        <ul>
                            <li>Keep you logged in</li>
                            <li>Remember your shopping cart</li>
                            <li>Understand your preferences</li>
                            <li>Improve site functionality</li>
                            <li>Show relevant advertisements</li>
                        </ul>
                        <p>You can manage cookie preferences through your browser settings.</p>
                    </div>
                    
                    <div class="privacy-section">
                        <h3>Third-Party Links</h3>
                        <p>Our website may contain links to third-party websites. We are not responsible for the privacy practices of these websites. We encourage you to read their privacy policies.</p>
                    </div>
                    
                    <div class="privacy-section">
                        <h3>Children\'s Privacy</h3>
                        <p>Our services are not intended for individuals under 18 years of age. We do not knowingly collect personal information from children.</p>
                    </div>
                    
                    <div class="privacy-section">
                        <h3>Policy Updates</h3>
                        <p>We may update this Privacy Policy from time to time. We will notify you of any material changes by posting the new policy on this page and updating the "Last Updated" date.</p>
                        <p><strong>Last Updated:</strong> April 2026</p>
                    </div>
                    
                    <div class="privacy-section">
                        <h3>Contact Us</h3>
                        <p>If you have questions about this Privacy Policy, please contact us:</p>
                        <ul>
                            <li><strong>Email:</strong> privacy@shirinfashion.com</li>
                            <li><strong>Phone:</strong> +880 1XXX-XXXXXX</li>
                            <li><strong>Address:</strong> Shirin Fashion, Dhaka, Bangladesh</li>
                        </ul>
                    </div>
                </div>',
                'is_active' => true,
                'sort_order' => 4,
            ],
            [
                'title' => 'Terms of Service',
                'slug' => 'terms',
                'content' => '<div class="terms-page">
                    <h2>Terms of Service</h2>
                    <p class="intro">Welcome to Shirin Fashion. By accessing and using our website, you agree to be bound by these Terms of Service. Please read them carefully before using our services.</p>
                    
                    <div class="terms-section">
                        <h3>Acceptance of Terms</h3>
                        <p>By accessing or using the Shirin Fashion website, you acknowledge that you have read, understood, and agree to be bound by these Terms of Service. If you do not agree to these terms, please do not use our website.</p>
                    </div>
                    
                    <div class="terms-section">
                        <h3>Use of Website</h3>
                        <p>You agree to use this website only for lawful purposes and in a manner that does not infringe on the rights of others. You agree not to:</p>
                        <ul>
                            <li>Use the website in any way that may damage, disable, overburden, or impair it</li>
                            <li>Attempt to gain unauthorized access to any part of the website</li>
                            <li>Use the website for any fraudulent or illegal purpose</li>
                            <li>Copy, modify, or distribute content from the website without our permission</li>
                            <li>Use automated systems or software to extract data from the website</li>
                        </ul>
                    </div>
                    
                    <div class="terms-section">
                        <h3>Account Registration</h3>
                        <ul>
                            <li>You must provide accurate and complete information when creating an account</li>
                            <li>You are responsible for maintaining the security of your account credentials</li>
                            <li>You must be at least 18 years old to create an account</li>
                            <li>You agree to notify us immediately of any unauthorized use of your account</li>
                            <li>We reserve the right to terminate accounts that violate our terms</li>
                        </ul>
                    </div>
                    
                    <div class="terms-section">
                        <h3>Orders and Payments</h3>
                        <ul>
                            <li>All orders are subject to availability and confirmation</li>
                            <li>Prices are subject to change without notice</li>
                            <li>We reserve the right to refuse or cancel any order</li>
                            <li>Payment must be made at the time of order</li>
                            <li>We accept all major payment methods as listed on our website</li>
                            <li>By placing an order, you confirm that you are authorized to use the payment method</li>
                        </ul>
                    </div>
                    
                    <div class="terms-section">
                        <h3>Pricing and Product Information</h3>
                        <ul>
                            <li>We strive to display accurate product information and prices</li>
                            <li>We reserve the right to correct any errors in pricing or product descriptions</li>
                            <li>Product colors may vary slightly due to monitor settings</li>
                            <li>We reserve the right to limit quantities of any products</li>
                            <li>All products are subject to availability</li>
                        </ul>
                    </div>
                    
                    <div class="terms-section">
                        <h3>Shipping and Delivery</h3>
                        <ul>
                            <li>Shipping times are estimates and not guaranteed</li>
                            <li>Risk of loss transfers to you upon delivery to the carrier</li>
                            <li>You are responsible for providing accurate shipping information</li>
                            <li>Additional fees may apply for remote or international deliveries</li>
                        </ul>
                    </div>
                    
                    <div class="terms-section">
                        <h3>Returns and Refunds</h3>
                        <ul>
                            <li>Our return policy is as stated in our Returns & Exchanges page</li>
                            <li>Items must be in original condition with tags attached</li>
                            <li>Refunds are processed within 5-7 business days after inspection</li>
                            <li>Original shipping charges are non-refundable unless the item is defective</li>
                        </ul>
                    </div>
                    
                    <div class="terms-section">
                        <h3>Intellectual Property</h3>
                        <ul>
                            <li>All content on this website is the property of Shirin Fashion</li>
                            <li>Our logo, graphics, and designs are protected by copyright</li>
                            <li>You may not copy, reproduce, or distribute our content without permission</li>
                            <li>You may not use our trademarks without prior written consent</li>
                        </ul>
                    </div>
                    
                    <div class="terms-section">
                        <h3>User Content</h3>
                        <ul>
                            <li>You are responsible for any content you submit to the website</li>
                            <li>You represent that you have the right to use any content you submit</li>
                            <li>We reserve the right to remove user content that violates our terms</li>
                            <li>You grant us a license to use any content you submit</li>
                        </ul>
                    </div>
                    
                    <div class="terms-section">
                        <h3>Limitation of Liability</h3>
                        <ul>
                            <li>The website is provided "as is" without warranties of any kind</li>
                            <li>We do not guarantee the website will be error-free or uninterrupted</li>
                            <li>We are not liable for any indirect, incidental, or consequential damages</li>
                            <li>Our liability is limited to the maximum extent permitted by law</li>
                        </ul>
                    </div>
                    
                    <div class="terms-section">
                        <h3>Indemnification</h3>
                        <p>You agree to indemnify and hold Shirin Fashion harmless from any claims, damages, or expenses arising from your use of the website or violation of these terms.</p>
                    </div>
                    
                    <div class="terms-section">
                        <h3>Governing Law</h3>
                        <p>These Terms of Service are governed by the laws of Bangladesh. Any disputes arising from these terms shall be resolved in the courts of Dhaka, Bangladesh.</p>
                    </div>
                    
                    <div class="terms-section">
                        <h3>Changes to Terms</h3>
                        <p>We reserve the right to modify these Terms of Service at any time. Changes will be posted on this page. Your continued use of the website constitutes acceptance of any changes.</p>
                    </div>
                    
                    <div class="terms-section">
                        <h3>Contact Us</h3>
                        <p>If you have questions about these Terms of Service, please contact us:</p>
                        <ul>
                            <li><strong>Email:</strong> legal@shirinfashion.com</li>
                            <li><strong>Phone:</strong> +880 1XXX-XXXXXX</li>
                        </ul>
                    </div>
                </div>',
                'is_active' => true,
                'sort_order' => 5,
            ],
        ];

        foreach ($pages as $page) {
            Page::updateOrCreate(
                ['slug' => $page['slug']],
                $page
            );
        }

        $this->command->info('Pages seeded successfully!');
    }
}
