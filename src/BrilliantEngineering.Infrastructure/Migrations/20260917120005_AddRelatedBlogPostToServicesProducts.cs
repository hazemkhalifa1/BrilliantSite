using Microsoft.EntityFrameworkCore.Migrations;

#nullable disable

namespace BrilliantEngineering.Infrastructure.Migrations
{
    /// <inheritdoc />
    public partial class AddRelatedBlogPostToServicesProducts : Migration
    {
        /// <inheritdoc />
        protected override void Up(MigrationBuilder migrationBuilder)
        {
            migrationBuilder.AddColumn<int>(
                name: "RelatedBlogPostId",
                table: "Services",
                type: "int",
                nullable: true);

            migrationBuilder.AddColumn<int>(
                name: "RelatedBlogPostId",
                table: "Products",
                type: "int",
                nullable: true);

            migrationBuilder.CreateIndex(
                name: "IX_Services_RelatedBlogPostId",
                table: "Services",
                column: "RelatedBlogPostId");

            migrationBuilder.CreateIndex(
                name: "IX_Products_RelatedBlogPostId",
                table: "Products",
                column: "RelatedBlogPostId");

            migrationBuilder.AddForeignKey(
                name: "FK_Products_BlogPosts_RelatedBlogPostId",
                table: "Products",
                column: "RelatedBlogPostId",
                principalTable: "BlogPosts",
                principalColumn: "Id",
                onDelete: ReferentialAction.SetNull);

            migrationBuilder.AddForeignKey(
                name: "FK_Services_BlogPosts_RelatedBlogPostId",
                table: "Services",
                column: "RelatedBlogPostId",
                principalTable: "BlogPosts",
                principalColumn: "Id",
                onDelete: ReferentialAction.SetNull);
        }

        /// <inheritdoc />
        protected override void Down(MigrationBuilder migrationBuilder)
        {
            migrationBuilder.DropForeignKey(
                name: "FK_Products_BlogPosts_RelatedBlogPostId",
                table: "Products");

            migrationBuilder.DropForeignKey(
                name: "FK_Services_BlogPosts_RelatedBlogPostId",
                table: "Services");

            migrationBuilder.DropIndex(
                name: "IX_Services_RelatedBlogPostId",
                table: "Services");

            migrationBuilder.DropIndex(
                name: "IX_Products_RelatedBlogPostId",
                table: "Products");

            migrationBuilder.DropColumn(
                name: "RelatedBlogPostId",
                table: "Services");

            migrationBuilder.DropColumn(
                name: "RelatedBlogPostId",
                table: "Products");
        }
    }
}
